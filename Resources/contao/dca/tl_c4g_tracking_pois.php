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
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_c4g_tracking_pois'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_c4g_tracking',
		'enableVersioning'            => true,
		'closed'                      => true,
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
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('tstamp DESC', 'id DESC'),
			'panelLayout'             => 'filter;sort,search,limit',
			'headerFields'            => array('name', 'tstamp'),
			//'child_record_callback'   => array('tl_module', 'listModule'),
			//'child_record_class'      => 'no_padding'
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s',
			//'label_callback'          => array('tl_theme', 'addPreviewImage')
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
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.svg',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array('visibility'),
		'default'                     => '{title_legend},name,uuid;{position_legend},latitude,longitude;{comment_legend:hide},comment;{user_legend},member,visibility;{delete_legend:hide},forDelete',
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
			'foreignKey'              => 'tl_c4g_tracking.name',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'lazy')
		),
		'tstamp' => array
		(
  		'flag'                    => 6,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['name'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
        'uuid' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['uuid'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'maxlength'=>23, 'readonly'=>true),
            'sql'                     => "varchar(23) NOT NULL default ''"
        ),
        'comment' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['comment'],
            'exclude'                 => true,
            'inputType'               => 'textarea',
            'search'                  => true,
            'eval'                    => array('style'=>'height:60px', 'decodeEntities'=>true),
            'sql'                     => "text NULL"
        ),
        'member' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['member'],
            'exclude'                 => true,
            'inputType'               => 'select',
            'foreignKey'              => 'tl_member.username',
            'eval'                    => array('doNotCopy'=>true, 'mandatory'=>true, 'chosen'=>true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => array('type'=>'hasOne', 'load'=>'eager')
        ),
        'visibility' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['visibility'],
            'default'                 => 'privat',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options'                 => array('privat', 'membergroups','public','owngroups'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_module'],
            'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'groups' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['groups'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'foreignKey'              => 'tl_member_group.name',
            'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'w50'),
            'sql'                     => "blob NULL",
            'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
        ),
        'trackUuid' => array
		(
			'foreignKey'              => 'tl_c4g_tracking_track.name',
			'sql'                     => "varchar(23) NOT NULL default ''"
		),
        'forDelete' => array
        (
          	'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_pois']['forDelete'],
          	'exclude'                 => true,
          	'inputType'               => 'checkbox',
          	'eval'                    => array('tl_class'=>'w50'),
          	'sql'                     => "char(1) NOT NULL default '0'"
        ),
		'positionId' => array(
			'foreignKey'              => 'tl_c4g_tracking_positions.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'hasOne', 'load'=>'eager')
		),
        /*'imei' => array(
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'batterystatus' => array(
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'networkinfo' => array(
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),*/
	)
);


/**
 * Class tl_c4g_tracking_devices
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_tracking
 * @author    Janosch Oltmanns
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2018
 */
class tl_c4g_tracking_pois extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	/**
	 * Check permissions to edit the table
	 */
	public function checkPermission()
	{
		if ($this->User->isAdmin)
		{
			return;
		}

        /*if (!$this->User->hasAccess('modules', 'themes'))
		{
			$this->log('Not enough permissions to access the modules module', __METHOD__, TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}*/
	}


	/**
	 * Return all front end modules as array
	 * @return array
	 */
	public function getTypes()
	{
		$groups = array();

		foreach ($GLOBALS['FE_MOD'] as $k=>$v)
		{
			foreach (array_keys($v) as $kk)
			{
				$groups[$k][] = $kk;
			}
		}

		return $groups;
	}


	/**
	 * List a front end module
	 * @param array
	 * @return string
	 */
	public function listDevices($row)
	{
		return '<div style="float:left">'. $row['name'] .' <span style="color:#b3b3b3;padding-left:3px">['. (isset($GLOBALS['TL_LANG']['FMD'][$row['type']][0]) ? $GLOBALS['TL_LANG']['FMD'][$row['type']][0] : $row['type']) .']</span>' . "</div>\n";
	}
}
