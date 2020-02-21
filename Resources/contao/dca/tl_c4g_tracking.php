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
 * Table tl_comments
 */

use con4gis\CoreBundle\Classes\C4GVersionProvider;

$GLOBALS['TL_DCA']['tl_c4g_tracking'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
        'ctable'                      => array('tl_c4g_tracking_pois', 'tl_c4g_tracking_tracks', 'tl_c4g_tracking_devices'),
        'enableVersioning'            => true,
        'onload_callback'             => array
        (
            array('tl_c4g_tracking', 'showConfigHint'),
            array('tl_c4g_tracking', 'adjustOperationDca')
        ),
        'onsubmit_callback'           => array(
            array('\con4gis\MapsBundle\Classes\Caches\C4GMapsAutomator', 'purgeLayerApiCache')
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
			'fields'                  => array('name'),
			'flag'                    => 1,
			'panelLayout'             => 'sort,search,limit',
            'icon'                    => 'bundles/con4giscore/images/be-icons/con4gis_blue.svg',
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s',
			//'label_callback'          => array('tl_theme', 'addPreviewImage')
		),
		'global_operations' => array
		(
			'all' => [
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			],
            'back' => [
                'href'                => 'key=back',
                'class'               => 'header_back',
                'button_callback'     => ['\con4gis\CoreBundle\Classes\Helper\DcaHelper', 'back'],
                'icon'                => 'back.svg',
                'label'               => &$GLOBALS['TL_LANG']['MSC']['backBT'],
            ],
		),
		'operations' => array
		(
            'pois' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['pois'],
                'href'                => 'table=tl_c4g_tracking_pois',
                'icon'                => 'bundles/con4gistracking/images/be-icons/location_flag.svg',
                //'button_callback'     => array('tl_theme', 'editModules')
            ),
            'tracks' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['tracks'],
                'href'                => 'table=tl_c4g_tracking_tracks',
                'icon'                => 'bundles/con4gistracking/images/be-icons/location_track.svg',
                //'button_callback'     => array('tl_theme', 'editLayout')
            ),
            'devices' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['devices'],
                'href'                => 'table=tl_c4g_tracking_devices',
                'icon'                => 'bundles/con4gistracking/images/be-icons/icon-devices.svg',
                'button_callback'     => array('tl_c4g_tracking', 'deviceButton')
            ),
            /*'boxes' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['boxes'],
                'href'                => 'table=tl_c4g_tracking_boxes',
                'icon'                => 'system/modules/con4gis_tracking/assets/icon-devices.svg',
            ),*/
            'edit' => array
            (
                'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['edit'],
                'href'                => 'act=edit',
                'icon'                => 'edit.svg',
            ),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.svg',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.svg',
				'attributes'          => 'style="margin-right:3px"'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
        '__selector__'                => array('useSmsGateway', 'adjustAdditionalData', 'usePushNotifications', 'limitAccess'),
		'default'                     => '{title_legend},name;{config_legend},httpGatewayInterval,apiKey;{sms_gateway_legend},useSmsGateway;{additional_data_legend},adjustAdditionalData;{push_notifications_legend},usePushNotifications;{access_legend:hide},limitAccess'
	),

	// Subpalettes
	'subpalettes' => array
	(
		'useSmsGateway'               => 'smsGatewayInterval,smsGatewayNumber,disableHttpGateway',
		'adjustAdditionalData'        => 'additionalData',
		'usePushNotifications'        => 'pushGcmApiKey',
        'limitAccess'                 => 'accessGroups'
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
        'importId' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['name'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'search'                  => true,
			'eval'                    => array('mandatory'=>true, 'unique'=>true, 'decodeEntities'=>true, 'maxlength'=>128, 'tl_class'=>'w50'),
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
        'httpGatewayInterval' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['httpGatewayInterval'],
            'default'                 => '10',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options'                 => array('0', '1', '2', '3', '5', '10', '30', '60', '120'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['gatewayIntervalOptions'],
            'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true),
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'apiKey' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['apiKey'],
            'inputType'               => 'text',
            'exclude'                 => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'search'                  => true,
            'eval'                    => array('mandatory'=>true, 'unique'=>true, 'decodeEntities'=>false, 'maxlength'=>128, 'tl_class'=>'w50'),
            'sql'                     => "varchar(128) NOT NULL default ''"
        ),
        'useSmsGateway' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['useSmsGateway'],
           	'exclude'                 => true,
           	'inputType'               => 'checkbox',
           	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
           	'sql'                     => "char(1) NOT NULL default ''"
        ),
        'smsGatewayInterval' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['smsGatewayInterval'],
            'default'                 => '60',
            'exclude'                 => true,
            'inputType'               => 'select',
            'options'                 => array('5', '10', '30', '60', '120'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['gatewayIntervalOptions'],
            'eval'                    => array('tl_class'=>'w50 clr', 'mandatory'=>true),
            'sql'                     => "varchar(32) NOT NULL default ''"
        ),
        'smsGatewayNumber' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['smsGatewayNumber'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>64, 'mandatory'=>true, 'tl_class'=>'w50'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'disableHttpGateway' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['disableHttpGateway'],
           	'exclude'                 => true,
           	'inputType'               => 'checkbox',
           	'eval'                    => array('tl_class'=>'w50'),
           	'sql'                     => "char(1) NOT NULL default ''"
        ),
        'adjustAdditionalData' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['adjustAdditionalData'],
           	'exclude'                 => true,
           	'inputType'               => 'checkbox',
           	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
           	'sql'                     => "char(1) NOT NULL default ''"
        ),
        'additionalData' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['additionalData'],
            'exclude'                 => true,
            'inputType'               => 'checkboxWizard',
            'options'                 => array('imei', 'batterystatus', 'networkinfo', 'positionaccuracy', 'positionspeed', 'positiontype'),
            'reference'               => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['additionalDataOptions'],
            'eval'                    => array('multiple'=>true, 'tl_class'=>'clr'),
            'sql'                     => "blob NULL"
        ),
        'usePushNotifications' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['usePushNotifications'],
           	'exclude'                 => true,
           	'inputType'               => 'checkbox',
           	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
           	'sql'                     => "char(1) NOT NULL default ''"
        ),
        'pushGcmApiKey' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['pushGcmApiKey'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => array('maxlength'=>64, 'mandatory'=>true, 'tl_class'=>'w50 clr'),
            'sql'                     => "varchar(64) NOT NULL default ''"
        ),
        'limitAccess' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['limitAccess'],
           	'exclude'                 => true,
           	'inputType'               => 'checkbox',
           	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
           	'sql'                     => "char(1) NOT NULL default ''"
        ),
        'accessGroups' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking']['accessGroups'],
            'exclude'                 => true,
            'inputType'               => 'checkbox',
            'foreignKey'              => 'tl_member_group.name',
            'eval'                    => array('mandatory'=>true, 'multiple'=>true, 'tl_class'=>'clr'),
            'sql'                     => "blob NULL",
            'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
        )
	)
);


/**
 * Class tl_c4g_tracking
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_tracking
 * @author    Janosch Oltmanns
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 201
 */
class tl_c4g_tracking extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

    public function deviceButton($row, $href, $label, $title, $icon, $attributes)
    {

        if (C4GVersionProvider::isInstalled('con4gis/tracking-android') || C4GVersionProvider::isInstalled('con4gis/tracking-boxes'))
        {
            $href .= '&amp;id='.$row['id'];

            return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
        }
        else
        {
            return '';
        }

    }

    public function adjustOperationDca($dc)
    {

    }

    /**
     * Show a hint if a track-configuration is not set in the root-page-settings
     * @param object
     */
    public function showConfigHint($dc)
    {
        if (Input::get('act') == 'edit')
        {
            return;
        }

        //$objTrackingConfigs = \C4gTrackingModel::findAll();
        Message::addInfo($GLOBALS['TL_LANG']['c4gTracking']['tracking_hint']);
    }
}