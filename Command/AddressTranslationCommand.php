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
use con4gis\TrackingPortalBundle\Resources\contao\models\C4gTrackingPortalPositionsModel;
use Contao\Database;
use Contao\CoreBundle\Framework\ContaoFramework;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
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
            $output->writeln("No profile found in the settings! Aborting...");
            return Command::INVALID;
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
        // get position data without addresses
        $arrPositions = $this->db->prepare("SELECT DISTINCT latitude, longitude FROM tl_c4g_tracking_positions WHERE COALESCE(address, '') = '' AND serverTstamp > 1688162400 LIMIT $chunksize")
            ->execute()->fetchAllAssoc();
        $output->writeln("Anzahl datensätze ist " . count($arrPositions));
        $model = new C4gTrackingPortalPositionsModel();
        foreach ($arrPositions as $key => $position) {
            if ($position['latitude'] && $position['longitude']) {
                    $address = C4gTrackingPortalPositionsModel::lookupCache($this->db, $position['latitude'], $position['longitude']);
                    if ($address == 'not cached') {
                        $address =  C4GUtils::reverseGeocode([$position['longitude'], $position['latitude']], true);
                    }
                if ($address) {
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
                $output->writeln("Could not convert coordinates to address for position (" . $position['longitude'] . " , " . $position['latitude'] . ")");
            } else {
                // update database
                $result = $this->db->prepare("UPDATE tl_c4g_tracking_positions SET address=? WHERE longitude =? AND latitude =? AND address=''")->execute($position['address'], $position['longitude'], $position['latitude']);
                $output->writeln("Updated address for position (" . $position['longitude'] . " , " . $position['latitude'] . ")");
                $counter ++;
            }
        }
        $output->writeln("Translated adDresseses.");
        $output->writeln("Processed " . $counter . " positions");
        return  Command::SUCCESS;
    }
}
