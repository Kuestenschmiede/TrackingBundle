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
 * Table tl_c4g_tracking_addresses
 */
$GLOBALS['TL_DCA']['tl_c4g_tracking_addresses'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => '',
		'dynamicPtable'               => true,
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
        'address' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'maxlength'=>23, 'readonly'=>true),
            'sql'                     => "varchar(255) NOT NULL default ''"
        ),
        'latitude' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "double NULL"
        ),
        'longitude' => array
        (
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "double NULL"
        ),

	)
);
