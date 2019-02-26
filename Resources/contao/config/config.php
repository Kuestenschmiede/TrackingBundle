<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
/**
 * Global settings
 */
$GLOBALS['con4gis']['tracking']['installed'] = true;

/**
 * Frontend Modules
 */
array_insert( $GLOBALS['FE_MOD']['con4gis'], $GLOBALS['con4gis']['maps']['installed']?1:0, array
  (
  'c4g_ssologin'   => 'con4gis\TrackingBundle\Resources\contao\modules\ModuleSsoLogin',
  'c4g_tracklist'  => 'con4gis\TrackingBundle\Resources\contao\modules\ModuleTrackList',
  'c4g_trackedit'  => 'con4gis\TrackingBundle\Resources\contao\modules\ModuleTrackEdit'
  )
);


/**
 * Backend Modules
 */
array_insert($GLOBALS['BE_MOD']['con4gis_bricks'], 7, array
(
	'c4g_tracking' => array
	(
        'tables'      => array
        (
            'tl_c4g_tracking',
            'tl_c4g_tracking_devices',
            'tl_c4g_tracking_pois',
            'tl_c4g_tracking_tracks',
            'tl_c4g_tracking_positions',
            'tl_c4g_tracking_boxes',
            'tl_c4g_tracking_box_locations'
        ),
        //'icon' => 'system/modules/con4gis_tracking/assets/tracking.png',
    )
));


$GLOBALS['c4g_tracking_devicetypes'] = array();
$GLOBALS['con4gis']['tracking']['apiBaseUrl'] = 'con4gis/api';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['c4gAddLocationsParent']['tracking'] = array('con4gis\TrackingBundle\Resources\contao\classes\TrackingFrontend','addLocations');
$GLOBALS['TL_HOOKS']['c4gPostGetInfoWindowContent']['tracking'] = array('con4gis\TrackingBundle\Resources\contao\classes\TrackingFrontend','getInfoWindowContent');
$GLOBALS['TL_CRON']['daily'][] = array('con4gis\TrackingBundle\Resources\contao\classes\TrackingFrontend', 'runCronJob');

/**
 * Rest-API
 */
$GLOBALS['TL_API']['trackingService'] = 'con4gis\TrackingBundle\Resources\contao\classes\TrackingService';

$GLOBALS['c4g_locationtypes'][] = 'tPois';
$GLOBALS['c4g_locationtypes'][] = 'tTracks';
$GLOBALS['c4g_locationtypes'][] = 'tLive';


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_c4g_tracking_boxes'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingBoxesModel';
$GLOBALS['TL_MODELS']['tl_c4g_tracking_boxlocations'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingBoxlocationModel';
$GLOBALS['TL_MODELS']['tl_c4g_tracking_devices'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel';
$GLOBALS['TL_MODELS']['tl_c4g_tracking'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingModel';
$GLOBALS['TL_MODELS']['tl_c4g_tracking_pois'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel';
$GLOBALS['TL_MODELS']['tl_c4g_tracking_positions'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPositionsModel';
$GLOBALS['TL_MODELS']['tl_c4g_tracking_tracks'] ='con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel';