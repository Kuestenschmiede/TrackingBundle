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
namespace con4gis\TrackingBundle\Resources\contao\classes;

use con4gis\MapsBundle\Classes\Events\LoadMapResourcesEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class TrackingPluginLoader
 * @package c4g\Tracking
 */
class TrackingPluginLoader
{

    public function loadTrackingPlugin(
        LoadMapResourcesEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {

        // load language script
        if ($GLOBALS['TL_LANGUAGE'] == 'de') {
            $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilterconstant-i18n'] = 'bundles/con4gistracking/js/c4g-maps-plugin-trackingfilter-constant-i18n-de.js';
        } else {
            // use english as fallback
            $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilterconstant-i18n'] = 'bundles/con4gistracking/js/c4g-maps-plugin-trackingfilter-constant-i18n-en.js';
        }


        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilter-constant'] = 'bundles/con4gistracking/js/c4g-maps-plugin-trackingfilter-constant.js';

        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilter-jquery-simple-datetimepicker'] = 'bundles/con4gistracking/js/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js';

        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilter'] = 'bundles/con4gistracking/js/c4g-maps-plugin-trackingdatafilter.js';

        $GLOBALS['TL_CSS']['c4g-maps-plugin-trackingdatafilter-jquery-simple-datetimepicker'] = 'bundles/con4gistracking/js/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.css';
        $GLOBALS['TL_CSS']['c4g-maps-plugin-trackingdatafilter'] = 'bundles/con4gistracking/css/c4g-maps-plugin-trackingdatafilter.css';

    }
}
