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
/**
 * Table tl_c4g_maps
 */

$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['__selector__'][] = 'memberVisibility';
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['__selector__'][] = 'useDatabaseStatus';
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['__selector__'][] = 'liveTrackingType';
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['__selector__'][] = 'isFilterable';
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['__selector__'][] = 'useIgnitionStatusStyle';

$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tPois'] = '{general_legend},name,profile,profile_mobile,published;{map_legend},is_map;{location_legend},location_type,tDontShowIfEmpty,memberVisibility,useDatabaseStatus,locstyle,data_layername,data_hidelayer,popup_info,routing_to,loc_linkurl,loc_onclick_zoomto,loc_minzoom,loc_maxzoom;{protection_legend:hide},protect_element;';

$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tTracks'] = '{general_legend},name,profile,profile_mobile,published;{map_legend},is_map;{location_legend},location_type,tDontShowIfEmpty,memberVisibility,useDatabaseStatus,locstyle,data_layername,data_hidelayer,loc_onclick_zoomto,loc_minzoom,loc_maxzoom;{protection_legend:hide},protect_element;';

$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tBoxes'] = '{general_legend},name,profile,profile_mobile,published;{map_legend},is_map;{location_legend},location_type,tDontShowIfEmpty,memberVisibility,useDatabaseStatus,locstyle,data_layername,data_hidelayer,loc_onclick_zoomto,loc_minzoom,loc_maxzoom;{protection_legend:hide},protect_element;';


$defaultTrackingLivePalette = '{general_legend},name,profile,profile_mobile,published;{map_legend},is_map;{location_legend},location_type,memberVisibility,useDatabaseStatus,locstyle,data_layername,data_hidelayer,loc_only_in_parent,loc_minzoom,loc_maxzoom,enablePopup,popupType,popup_info;{live_tracking_legend},liveTrackingType,isFilterable,useIgnitionStatusStyle;{protection_legend:hide},protect_element;';


$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive'] = $defaultTrackingLivePalette;

$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive_all'] = $defaultTrackingLivePalette;
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive_alleach'] = $defaultTrackingLivePalette;
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive_group'] = $defaultTrackingLivePalette;
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive_groupeach'] = $defaultTrackingLivePalette;
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive_device'] = str_replace('liveTrackingType','liveTrackingType,liveTrackingDevices', $defaultTrackingLivePalette);
$GLOBALS['TL_DCA']['tl_c4g_maps']['palettes']['tLive_deviceeach'] = str_replace('liveTrackingType','liveTrackingType,liveTrackingDevices', $defaultTrackingLivePalette);

$GLOBALS['TL_DCA']['tl_c4g_maps']['subpalettes']['memberVisibility_specialGroups'] = 'specialGroups';
$GLOBALS['TL_DCA']['tl_c4g_maps']['subpalettes']['memberVisibility_specialMember'] = 'specialMembers';
$GLOBALS['TL_DCA']['tl_c4g_maps']['subpalettes']['useDatabaseStatus'] = 'databaseStatus';
$GLOBALS['TL_DCA']['tl_c4g_maps']['subpalettes']['isFilterable'] = 'filterLocationStyle';
$GLOBALS['TL_DCA']['tl_c4g_maps']['subpalettes']['useIgnitionStatusStyle'] = 'ignitionStatusStyleOn,ignitionStatusStyleOff';

/*
 * Darstellungsart
 *
 * zusammengefasste Darstellung aller Datensätze
 * einzeln aufgeführte Kind-Elemente aller Datensätze
 * zusammengefasste Darstellung einzelner Gruppen
 * einzeln aufgeführte Kind-Elemente einzelner Gruppen
 * einzelnes Gerät
 *
 */

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['liveTrackingDevices'] = array
(
  'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['liveTrackingDevices'],
  'exclude'                 => true,
  'filter'                  => true,
  'inputType'               => 'checkboxWizard',
  'foreignKey'              => 'tl_c4g_tracking_devices.name',
  'options_callback'        => array('tl_c4g_maps_tracking', 'getDevices'),
  'eval'                    => array('multiple'=>true, 'tl_class'=>'clr'),
  'sql'                     => "blob NULL",
  'relation'                => array('type'=>'belongsToMany', 'load'=>'lazy')
);

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['liveTrackingType'] = array
(
  'label'     => &$GLOBALS['TL_LANG']['tl_c4g_maps']['liveTrackingType'],
  'inputType' => 'select',
  'exclude'   => true,
  'sorting'   => true,
  'flag'      => 1,
  'options'   => array('tLive_all', 'tLive_alleach', 'tLive_group', 'tLive_groupeach', 'tLive_device', 'tLive_deviceeach'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes'],
  'eval'      => array('includeBlankOption' => true, 'submitOnChange' => true, 'mandatory' => true, 'tl_class'           => 'w50'),
  'sql'       => "varchar(48) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['isFilterable'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['isFilterable'],
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr w50'),
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['filterLocationStyle'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['filterLocationStyle'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_c4g_maps','getLocStyles'),
    'eval'                    => array('tl_class'=>'w50'),
    'wizard' => array
    (
        array('tl_c4g_maps', 'editLocationStyle')
    ),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['tDontShowIfEmpty'] = array
(
  'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['tDontShowIfEmpty'],
  'exclude'                 => true,
  'filter'                  => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('submitOnChange'=>false, 'tl_class'=>'clr'),
  'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['memberVisibility'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['memberVisibility'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options'                 => array('own','ownGroups','specialGroups','specialMember','all'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['memberVisibility'],
    'eval'                    => array('includeBlankOption'=>true, 'submitOnChange'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(32) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['useDatabaseStatus'] = array
(
  'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['useDatabaseStatus'],
  'exclude'                 => true,
  'filter'                  => true,
  'inputType'               => 'checkbox',
  'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
  'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['databaseStatus'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['databaseStatus'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'options'                 => array('privat','membergroups','owngroups','public'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['databaseStatus'],
    'eval'                    => array('multiple'=>true, 'tl_class'=>'clr'),
    'sql'                     => "blob NULL"
);


// $GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['allowedGroups'] = array
// (
//       'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['allowedGroups'],
//       'exclude'                 => true,
// 			'inputType'               => 'checkbox',
// 			'foreignKey'              => 'tl_member_group.name',
// 			'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'clr'),
// 			'sql'                     => "blob NULL"
// );

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['specialMembers'] = array
(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['specialMembers'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_member.email',
			'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'clr'),
			'sql'                     => "blob NULL"
);
$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['specialGroups'] = array
(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['specialGroups'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'foreignKey'              => 'tl_member_group.name',
			'eval'                    => array('mandatory'=>false, 'multiple'=>true, 'tl_class'=>'clr'),
			'sql'                     => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['useIgnitionStatusStyle'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['useIgnitionStatusStyle'],
    'exclude'                 => true,
    'filter'                  => true,
    'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr'),
    'sql'                     => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['ignitionStatusStyleOn'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['ignitionStatusStyleOn'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_c4g_maps','getLocStyles'),
    'eval'                    => array('tl_class'=>'w50'),
    'wizard' => array
    (
        array('tl_c4g_maps', 'editLocationStyle')
    ),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);
$GLOBALS['TL_DCA']['tl_c4g_maps']['fields']['ignitionStatusStyleOff'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_maps']['ignitionStatusStyleOff'],
    'exclude'                 => true,
    'inputType'               => 'select',
    'options_callback'        => array('tl_c4g_maps','getLocStyles'),
    'eval'                    => array('tl_class'=>'w50'),
    'wizard' => array
    (
        array('tl_c4g_maps', 'editLocationStyle')
    ),
    'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

/**
 * Class tl_c4g_tracking_devices
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_tracking
 * @author    Janosch Oltmanns
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2018
 */
class tl_c4g_maps_tracking extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function getDevices()
    {

        $arrDevices = array();
        $objDevices = $this->Database->execute("SELECT * FROM tl_c4g_tracking_devices ORDER BY name");

        while ($objDevices->next())
        {
            $arrDevices[$objDevices->id] = ($objDevices->name ? $objDevices->name : ($objDevices->imei ? $objDevices->imei : 'No Device Name')) . ' (ID ' . $objDevices->id . ')';

        }

        return $arrDevices;
    }
}