<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\TrackingBundle\Command;


use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\MapsBundle\Resources\contao\models\C4gMapProfilesModel;
use con4gis\MapsBundle\Resources\contao\models\C4gMapSettingsModel;
use con4gis\TrackingPortalBundle\Resources\contao\models\C4gTrackingPortalPositionsModel;
use Contao\Database;
use Contao\CoreBundle\Framework\ContaoFramework;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class AddressTranslationCommand
 * Provides a executable command to translate the geographic coordinates in the tl_c4g_tracking_positions table
 * to actual addresses.
 * @package con4gis\TrackingBundle\Command
 */
class AddressTranslationCommand extends Command
{
    private ContaoFramework $framework;
    private Database $db;
    private LoggerInterface $logger;

    public function __construct(ContaoFramework $framework, LoggerInterface $logger)
    {
        $this->framework = $framework;
        $this->logger = $logger;
        if (!$this->framework->isInitialized()) {
            $this->framework->initialize();
        }
        $this->db = Database::getInstance();
        parent::__construct();

    }

    protected function configure()
    {
        $this->setName('tracking:translate-addresses')
            ->setDescription('Translates coordinates from the position table into addresses')
            ->setHelp('')
            ->addOption(
                "chunksize",
                null,
                InputArgument::OPTIONAL, "The amount of datasets that should be processed at once."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arrSettings = $this->db->execute("SELECT * FROM tl_c4g_settings LIMIT 1")->fetchAssoc();
        $profileId = $arrSettings['defaultprofile'];
        if (!$profileId) {
            // abort when no profile was found
            $this->infoLog("No profile found in the settings! Aborting...", $output);
            return 1;
        }
        $objMapsProfile = C4gMapProfilesModel::findBy('id', $profileId);
        if ($objMapsProfile) {
            if ($objMapsProfile->geopicker_anonymous) {
                $anonymous = true;
            }
        }
        $chunksize = $input->getOption("chunksize");
        if (!$chunksize) {
            $chunksize = 10000;
        } else {
            $chunksize = intval($chunksize);
        }
        $counter = 0;
        // minimum tstamp value where positions will be processed
        $thresholdTimestamp = 1704121931;
        // get position data where the address is shorter than 10 characters AND has comma -> probably broken address string
        $arrPositions = $this->db->prepare("SELECT DISTINCT latitude, longitude, tstamp FROM tl_c4g_tracking_positions WHERE (address = '' OR (LENGTH(address) < 9 AND address LIKE '%,%')) AND serverTstamp > ? ORDER BY `tstamp` DESC LIMIT ?")
            ->execute($thresholdTimestamp, $chunksize)->fetchAllAssoc();
        $this->infoLog("Anzahl datensätze ist " . count($arrPositions), $output);
        $model = new C4gTrackingPortalPositionsModel();
        foreach ($arrPositions as $key => $position) {
            if ($position['latitude'] && $position['longitude']) {
                $address = $this->reverseGeocode([$position['longitude'], $position['latitude']], true);

                if ($address && is_array($address)) {
                    $strAddress =  '';
                    $strAddress .=  $address['street'];
                    if (!$anonymous) {
                        $strAddress .= $address['housenumber'] ? " " . $address['housenumber'] : "";
                    }
                    $strAddress .= $address['postalcode'] ? ", " . $address['postalcode'] : "";
                    $strAddress .= $address['localadmin'] ? " " . $address['localadmin'] : "";
                    $position['address'] = $strAddress;
                }
            }

            if ($position['address'] === "") {
                $this->infoLog("Could not convert coordinates to address for position (" . $position['longitude'] . " , " . $position['latitude'] . ")", $output);
            } else if ($position['address'] !== null && is_string($position['address'])) {
                // update database
                $this->infoLog("Determined address: " . $position['address'], $output);
                $result = $this->db->prepare("UPDATE tl_c4g_tracking_positions SET address=? WHERE longitude =? AND latitude =?")->execute($position['address'], $position['longitude'], $position['latitude']);
                $this->infoLog("Updated address for position (" . $position['longitude'] . " , " . $position['latitude'] . ")", $output);
                $counter ++;
            } else {
                $this->logger->error("Got Address '" . $position['address'] . "' which is not a valid address string.");
            }
        }
        $this->infoLog("Translated addresses.", $output);
        $this->infoLog("Processed " . $counter . " positions", $output);
        return 0;
    }

    private function reverseGeocode($coordinates, $getArray)
    {
        $settings = C4gMapSettingsModel::findOnly();
        if ($settings->con4gisIoUrl && $settings->con4gisIoKey) {
            $searchUrl = rtrim($settings->con4gisIoUrl, '/') . '/';
            $searchUrl .= 'reverse.php?key=' . $settings->con4gisIoKey;
            $searchUrl .= '&lon=' . $coordinates[0] . '&lat=' . $coordinates[1] . '&format=json';

            $headers = [];
            if ($_SERVER['HTTP_REFERER']) {
                $headers['Referer'] = $_SERVER['HTTP_REFERER'];
            }
            if ($_SERVER['HTTP_USER_AGENT']) {
                $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $client = HttpClient::create([
                'headers' => $headers,
            ]);
            try {
                $response = $client->request('GET', $searchUrl, ['timeout' => 2]);
                $statusCode = $response->getStatusCode();
                if ($response && $response->getStatusCode() === 200) {
                    $response = $response->getContent();
                    $response = \GuzzleHttp\json_decode($response, true);
                    if ($getArray) {
                        return $response['address'];
                    } else {
                        return $response['display_name'];
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
                return false;
            }
        }
    }

    private function infoLog(string $message, OutputInterface $output)
    {
        $output->writeln(date('d.m.Y H:i:s') . ': ' . $message);
    }
}
