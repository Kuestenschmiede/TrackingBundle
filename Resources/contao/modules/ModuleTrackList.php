<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\TrackingBundle\Resources\contao\modules;


/**
 * Class ModuleTrackList
 * @package c4g
 */
class ModuleTrackList extends \Module
{

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'mod_tracklist';

  /**
   * Display a wildcard in the back end
   * @return string
   */
  public function generate()
  {
    if (TL_MODE == 'BE')
    {
      $objTemplate = new \BackendTemplate('be_wildcard');

      $objTemplate->wildcard = '### ModuleTrackList ###';
      $objTemplate->title = $this->headline;
      $objTemplate->id = $this->id;
      $objTemplate->link = $this->name;
      $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

      return $objTemplate->parse();
    }


    return parent::generate();
  }


  /**
   * Generate the module
   */
  protected function compile()
  {

    $arrData = array();

    $strWhere = " WHERE member=0";
    if (FE_USER_LOGGED_IN)
    {
      $this->import('FrontendUser', 'User');
      $strWhere = " WHERE member=" . $this->User->id;
    }

    if ($this->showWithoutFilter)
    {
      $strWhere = "";
    }


    if ($this->showTracks)
    {
      $objTracks = $this->Database->prepare("SELECT tl_c4g_tracking_tracks.*, CONCAT('track') as type, count(*) as count FROM tl_c4g_tracking_tracks LEFT JOIN tl_c4g_tracking_positions ON tl_c4g_tracking_positions.track_uuid=tl_c4g_tracking_tracks.uuid" . $strWhere . " GROUP BY tl_c4g_tracking_tracks.id")
                                  ->execute();
      if ($objTracks->numRows > 0)
      {
        $arrTracks = $objTracks->fetchAllAssoc();
        $arrData = array_merge($arrData, $arrTracks);
      }
    }
    if ($this->showPois)
    {
      $objPois = $this->Database->prepare("SELECT *, CONCAT('poi') as type FROM tl_c4g_tracking_pois" . $strWhere)
                                  ->execute();
      if ($objPois->numRows > 0)
      {
        $arrPois = $objPois->fetchAllAssoc();
        $arrData = array_merge($arrData, $arrPois);
      }
    }

    uasort($arrData, array($this, 'sortByTstamp'));

    $arrDataManipulatet = array();

    $blnUseEditLink = false;
    if (($objJumpTo = $this->objModel->getRelated('jumpTo')) !== null)
		{
      $blnUseEditLink = true;
      $arrJumpTo = $objJumpTo->row();
		}

    foreach ($arrData as $arrEntry)
    {

      $arrEntry['date'] = \Date::parse(\Date::getNumericDateFormat(),$arrEntry['tstamp']);
      $arrEntry['datim'] = \Date::parse(\Date::getNumericDatimFormat(),$arrEntry['tstamp']);
      if ($blnUseEditLink)
      {
        if ($this->editWithoutFilter || (FE_USER_LOGGED_IN && $this->User->id==$arrEntry['member']))
        {
          $arrEntry['editHref'] = ampersand($this->generateFrontendUrl($arrJumpTo, ((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . $arrEntry['type'] . '_' . $arrEntry['id']));
        }
      }

      //((\Config::get('useAutoItem') && !\Config::get('disableAlias')) ?  '/' : '/items/') . ((!\Config::get('disableAlias') && $objItem->alias != '') ? $objItem->alias : $objItem->id)));

      $arrDataManipulatet[] = $arrEntry;
    }

    //print_r($arrDataManipulatet);
    $this->Template->data = $arrDataManipulatet;

  }

  private function sortByTstamp($a, $b)
  {
    if ($a['tstamp'] == $b['tstamp']) {
        return 0;
    }
    return ($a['tstamp'] < $b['tstamp']) ? -1 : 1;
  }
}
