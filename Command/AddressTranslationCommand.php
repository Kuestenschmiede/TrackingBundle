<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 17.09.18
 * Time: 11:04
 */

namespace con4gis\TrackingBundle\Command;


use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use Contao\Database;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddressTranslationCommand
 * Provides a executable command to translate the geographic coordinates in the tl_c4g_tracking_positions table
 * to actual addresses.
 * @package con4gis\TrackingBundle\Command
 */
class AddressTranslationCommand extends ContainerAwareCommand
{
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
        $this->getContainer()->get('contao.framework')->initialize();
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $db = Database::getInstance();
        // TODO standardprofil aus den einstellungen holen
        $arrSettings = $db->execute("SELECT * FROM tl_c4g_settings LIMIT 1")->fetchAssoc();
        $profileId = $arrSettings['defaultprofile'];
        if (!$profileId) {
            // abort when no profile was found
            $output->writeln("No profile found in the settings! Aborting...");
            return;
        }
        $chunksize = $input->getOption("chunksize");
        if (!$chunksize) {
            $chunksize = 10000;
        } else {
            $chunksize = intval($chunksize);
        }
        $counter = 0;
        // TODO Wurf (10000 Datensätze?) aus der positionstabelle holen
        // get position data without addresses
        $arrPositions = $db->prepare("SELECT * FROM tl_c4g_tracking_positions WHERE COALESCE(address, '') = '' LIMIT ?")
            ->execute($chunksize)->fetchAllAssoc();
        $output->writeln("Anzahl datensätze ist " . count($arrPositions));
        // TODO daten verarbeiten ( adressen übersetzen)
        foreach ($arrPositions as $key => $position) {
            $position['address'] = C4GBrickCommon::convert_coordinates_to_address($position['latitude'], $position['longitude'], $profileId);
            if ($position['address'] === "") {
                $output->writeln("Could not convert coordinates to address for position ID " . $position['id']);
            } else {
                // update database
                $db->prepare("UPDATE tl_c4g_tracking_positions SET address=? WHERE id = ?")->execute($position['address'], $position['id']);
                $output->writeln("Updated address for position ID " . $position['id']);
                $counter++;
            }
        }
        $output->writeln("Translated adresseses.");
        $output->writeln("Processed " . $counter . " datasets");


        // TODO daten zurück in tabelle schreiben
        // TODO zurück zu 2.
    }

}