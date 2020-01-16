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
$GLOBALS['TL_DCA']['tl_c4g_tracking_boxes'] = array
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
            'flag'                    => 8,
            'fields'                  => array('tstamp'),
            'panelLayout'             => 'filter;sort,search,limit',
            'headerFields'            => array('name', 'tstamp'),
            //'child_record_callback'   => array('tl_module', 'listModule'),
            //'child_record_class'      => 'no_padding'
        ),
        'label' => array
        (
            'fields'                  => array('imei'),
            'format'                  => '%s',
            'label_callback'          => array('tl_c4g_tracking_boxes', 'listBoxes')
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
        '__selector__'                => array(''),
        'default'                     => '{title_legend},name,imei;',
    ),

    // Subpalettes
    'subpalettes' => array
    (

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
        'imei' => array
        (
            'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['imei'],
            'exclude'                 => true,
            'search'                  => true,
            'inputType'               => 'text',
            'eval'                    => array('tl_class'=>'w50 clr', 'mandatory'=>true, 'maxlength'=>15, 'doNotCopy'=>false),
            'sql'                     => "varchar(32) NOT NULL default ''"
        )
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
class tl_c4g_tracking_boxes extends Backend
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
     * List a front end module
     * @param array
     * @return string
     */
    public function listBoxes($row)
    {
        return '<div style="float:left">'. ($row['name'] ? ('<strong>Name: </strong>' . $row['name'] . '<br>') : '') . '<strong>IMEI: </strong>' . $row['imei'] .' <span style="color:#b3b3b3;padding-left:3px">['. $GLOBALS['TL_LANG']['tl_c4g_tracking_devices']['types'][$row['type']] .']</span>' . "</div>\n";
    }


}
