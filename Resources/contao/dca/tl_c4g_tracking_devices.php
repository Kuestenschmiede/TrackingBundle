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
$GLOBALS['TL_DCA']['tl_c4g_tracking_devices'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_c4g_tracking',
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			//array('tl_module', 'checkPermission')
		),
        'onsubmit_callback' => array
        (
            array('\con4gis\MapsBundle\Classes\Caches\C4GMapsAutomator', 'purgeLayerApiCache')
            //array('tl_c4g_tracking_devices', 'checkForPushNotifications')
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
            'flag'                    => 8,
			'fields'                  => array('name'),
			'panelLayout'             => 'filter;sort,search,limit',
			'headerFields'            => array('name', 'tstamp'),
			//'child_record_callback'   => array('tl_module', 'listModule'),
			//'child_record_class'      => 'no_padding'
		),
        'label' => array
      		(
      			'fields'                  => array('imei'),
      			'format'                  => '%s',
      			'label_callback'          => array('tl_c4g_tracking_devices', 'listDevices')
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
				'icon'                => 'edit.svg',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['copy'],
				'href'                => 'act=paste&amp;mode=copy',
				'icon'                => 'copy.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'cut' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['cut'],
				'href'                => 'act=paste&amp;mode=cut',
				'icon'                => 'cut.svg',
				'attributes'          => 'onclick="Backend.getScrollOffset()"'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
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
		'__selector__'			      => array('type'),
		'default'                     => '{title_legend},name,type',
	),

	// Subpalettes


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
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['name'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>false, 'maxlength'=>255),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['type'],
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'filter'                  => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_c4g_tracking_devices', 'getTypes'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['types'] ,
			'eval'                    => array('mandatory'=>true, 'helpwizard'=>false, 'chosen'=>false, 'submitOnChange'=>true, 'tl_class'=>'w50 clr', 'includeBlankOption'=>true),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'lastPositionId' => array
		(
			'label'					  => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['lastPositionId'],
			'inputType'			      => 'text',
			'eval'					  => array('readonly'=>true),
			'foreignKey'              => 'tl_c4g_tracking_positions.id',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'hasOne', 'load'=>'eager')
		),
		'mapStructureId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['mapStructureId'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'select',
			'foreignKey'              => 'tl_c4g_maps.name',
			'eval'                    => array('tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'hasOne', 'load'=>'eager')
		),
		'timeZone' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['timeZone'],
			'inputType'               => 'select',
			'default'			      => \Config::get('timeZone'),
			'options'                 => System::getTimeZones(),
			'eval'                    => array('chosen'=>true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'locationStyle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['locationStyle'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options_callback'        => array('tl_c4g_tracking_devices','getLocStyles'),
			'eval'                    => array('tl_class'=>'w50', 'chosen'=>true, 'includeBlankOption'=>true),
			'wizard' => array
			(
				array('tl_c4g_tracking_devices', 'editLocationStyle')
			),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
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
class tl_c4g_tracking_devices extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}


	public function getLocStyles(DataContainer $dc)
	{
		$profile = $this->Database->prepare("SELECT locstyles FROM tl_c4g_map_profiles WHERE id=?")->execute($dc->activeRecord->profile);
		$ids = deserialize($profile->locstyles,true);
		if (count($ids)>0) {
			$locStyles = $this->Database->prepare("SELECT id,name FROM tl_c4g_map_locstyles WHERE id IN (".implode(',',$ids).") ORDER BY name")->execute();
		} else {
			$locStyles = $this->Database->prepare("SELECT id,name FROM tl_c4g_map_locstyles ORDER BY name")->execute();
		}
		while ($locStyles->next()) {
			$return[$locStyles->id] = $locStyles->name;
		}
		return $return;
	}

	public function editLocationStyle(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=c4g_map_locstyles&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_c4g_maps']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_c4g_maps']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_c4g_maps']['editalias'][0], 'style="vertical-align:top"') . '</a>';
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

		return $GLOBALS['c4g_tracking_devicetypes'];

	}


	/**
	 * List a front end module
	 * @param array
	 * @return string
	 */
	public function listDevices($row)
	{
		return '<div style="float:left">'. ($row['name'] ? ('<strong>Name: </strong>' . $row['name'] . '<br>') : '') . '<strong>IMEI: </strong>' . $row['imei'] .' <span style="color:#b3b3b3;padding-left:3px">['. $GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['types'][$row['type']] .']</span>' . "</div>\n";
	}

    public function checkForPushNotifications($dc)
    {
        if (\Input::post('sendPushNotification') && \Input::post('pushNotificationContent')  && \Input::post('token'))
        {

            \con4gis\TrackingBundle\Classes\Tracking::sendPushNotificationByToken($dc->activeRecord->pid, $dc->activeRecord->type, \Input::post('token'), \Input::post('pushNotificationContent'));
        }



        $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findBy('id', $dc->id);
        $objDevice->sendPushNotification = "";
        $objDevice->pushNotificationContent = "";
        $objDevice->save();

    }
}
