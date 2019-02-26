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
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_c4g_tracking_boxlocations'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'ptable'                      => 'tl_c4g_tracking_boxes',
        'enableVersioning'            => true,
        'onload_callback' => array
        (
            //array('tl_module', 'checkPermission')
        ),
        'onsubmit_callback'           => array(
            array('\con4gis\CoreBundle\Resources\contao\classes\C4GAutomator', 'purgeApiCache')
        ),
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid' => 'index'
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode'                    => 4,
            'fields'                  => array('tstamp'),
            'panelLayout'             => 'filter;sort,search,limit',
            'headerFields'            => array('tstamp'),
            //'child_record_callback'   => array('tl_module', 'listModule'),
            //'child_record_class'      => 'no_padding'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.gif'
            ),
            'delete' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['delete'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        '__selector__'                => array(''),
        'default'                     => '{title_legend},track_uuid;{position_legend},location,accuracy,speed;',
    ),

    // Subpalettes
    'subpalettes' => array
    (
        'visibility_owngroups' => 'groups'
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ),
        'pid' => array
        (
            'foreignKey'              => 'tl_c4g_tracking_track.name',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
        ),
        'tstamp' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),

        'latitude' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['name'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "double NULL"
        ),
        'longitude' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['name'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "double NULL"
        ),

        'accuracy' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['name'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "float NULL"
        ),
        'speed' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['name'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "float NULL"
        ),
        'phoneNo' => array(
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'mileage' => array(
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'driverId' => array(
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'temperature' => array(
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'status' => array(
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),

        'imei' => array(
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
    )
);



