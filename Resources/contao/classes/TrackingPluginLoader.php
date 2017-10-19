<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\TrackingBundle\Resources\contao\classes;

/**
 * Class TrackingPluginLoader
 * @package c4g\Tracking
 */
class TrackingPluginLoader
{

    public function loadTrackingPlugin()
    {

        // load language script
        if ($GLOBALS['TL_LANGUAGE'] == 'de') {
            $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilterconstant-i18n'] = 'system/modules/con4gis_tracking/assets/js/c4g-maps-plugin-trackingfilter-constant-i18n-de.js';
        } else {
            // use english as fallback
            $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilterconstant-i18n'] = 'system/modules/con4gis_tracking/assets/js/c4g-maps-plugin-trackingfilter-constant-i18n-en.js';
        }


        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilter-constant'] = 'system/modules/con4gis_tracking/assets/js/c4g-maps-plugin-trackingfilter-constant.js';

        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilter-jquery-simple-datetimepicker'] = 'system/modules/con4gis_tracking/assets/js/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.js';

        $GLOBALS['TL_JAVASCRIPT']['c4g-maps-plugin-trackingdatafilter'] = 'system/modules/con4gis_tracking/assets/js/c4g-maps-plugin-trackingdatafilter.js';

        $GLOBALS['TL_CSS']['c4g-maps-plugin-trackingdatafilter-jquery-simple-datetimepicker'] = 'system/modules/con4gis_tracking/assets/js/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.css';
        $GLOBALS['TL_CSS']['c4g-maps-plugin-trackingdatafilter'] = 'system/modules/con4gis_tracking/assets/css/c4g-maps-plugin-trackingdatafilter.css';

    }
}
