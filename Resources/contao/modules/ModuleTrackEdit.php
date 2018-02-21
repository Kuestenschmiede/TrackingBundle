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
 * Class ModuleTrackEdit
 * @package c4g
 */
class ModuleTrackEdit extends \Module
{

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'mod_trackedit';
  protected $strType;
  protected $intTrackId;
  protected $intPoiId;

  /**
   * Display a wildcard in the back end
   * @return string
   */
  public function generate()
  {
    if (TL_MODE == 'BE')
    {
      $objTemplate = new \BackendTemplate('be_wildcard');

      $objTemplate->wildcard = '### ModuleTrackEdit ###';
      $objTemplate->title = $this->headline;
      $objTemplate->id = $this->id;
      $objTemplate->link = $this->name;
      $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

      return $objTemplate->parse();
    }


    if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
		{
			\Input::setGet('items', \Input::get('auto_item'));
		}

		if (!\Input::get('items'))
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}


		$strItem = \Input::get('items');

		if (strpos($strItem, "track")!==false)
		{
  		$this->strType = "track";
  		$this->intTrackId = str_replace("track_", "", $strItem);
		}
		if (strpos($strItem, "poi")!==false)
		{
  		$this->strType = "poi";
  		$this->intPoiId = str_replace("poi_", "", $strItem);
		}
		if (!$this->strType || $this->strType=="")
		{
			global $objPage;
			$objPage->noSearch = 1;
			$objPage->cache = 0;
			return '';
		}

    return parent::generate();
  }


  /**
   * Generate the module
   */
  protected function compile()
  {

    global $objPage;


		if (FE_USER_LOGGED_IN)
    {
      $this->import('FrontendUser', 'User');
    }

    switch ($this->strType)
    {
      case "track":
        $objData = $this->Database->prepare("SELECT * FROM tl_c4g_tracking_tracks WHERE id=? AND member=?")
                               ->execute($this->intTrackId, $this->User->id);
        break;
      case "poi":
        $objData = $this->Database->prepare("SELECT * FROM tl_c4g_tracking_pois WHERE id=? AND member=?")
                               ->execute($this->intPoiId, $this->User->id);
        break;
    }

		if ($objData->numRows == 0)
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			// Send a 404 header
			header('HTTP/1.1 404 Not Found');
			$this->Template->error = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
			return;
		}


		if (!$this->editWithoutFilter && (FE_USER_LOGGED_IN && $this->User->id!=$objData->member))
		{
			// Do not index or cache the page
			$objPage->noSearch = 1;
			$objPage->cache = 0;

			// Send a 404 header
			header('HTTP/1.1 404 Not Found');
			$this->Template->error = '<p class="error">' . sprintf($GLOBALS['TL_LANG']['MSC']['invalidPage'], \Input::get('items')) . '</p>';
			return;
		}

		$arrFields = $this->getFields($objData);
		$arrFieldsForTemplate = array();

		foreach ($arrFields as $arrField)
    {

      $strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];
      if (!$this->classFileExists($strClass))
      {
          continue;
      }

      $arrField['mandatory'] = $arrField['eval']['mandatory'];
      $arrField['tableless'] = true;

      $objWidget = new $strClass($arrField);

      if ($arrField['eval']['rgxp'])
      {
        $objWidget->rgxp = $arrField['eval']['rgxp'];
      }

      if ($arrField['eval']['maxlength'])
      {
        $objWidget->maxlength = $arrField['eval']['maxlength'];
      }

      if (!$arrField['id'] && $arrField['name'])
      {
        $objWidget->id = $arrField['name'];
      }

      if ($arrField['eval']['toggleFields'])
      {
        $objWidget->class = $objWidget->class ? ($objWidget->class . " toggle") : "toggle";
      }

      if ($arrField['eval']['toggleBy'])
      {
        $strToggleByClass = "toggleBy" . ucfirst($arrField['eval']['toggleBy']);

        $blnIsVisible = false;

        $arrToggleField = $this->arrFields[$arrField['eval']['toggleBy']];

        if ($arrToggleField['inputType'] == "checkbox")
        {
          if (\Input::post($arrToggleField['name']))
          {
            $blnIsVisible = true;

            if (!$arrField['eval']['mandatory'] && $arrField['eval']['toggleMandatory'])
            {
              $objWidget->mandatory = true;
            }
          }
        }

        if (!$blnIsVisible)
        {
          $strToggleByClass .= " toggleInvisible";
        }
        else
        {
          $strToggleByClass .= " toggleVisible";
        }

        $objWidget->class = $objWidget->class ? ($objWidget->class . " " . $strToggleByClass) : $strToggleByClass;
      }

      if ($this->Input->post('FORM_SUBMIT') == 'formTrackEdit')
      {
          $objWidget->validate();
          if ($objWidget->hasErrors())
          {
            $this->doNotSubmit = true;
          }
      }

      if ($arrField['eval']['toggleMandatory'] && !$objWidget->mandatory)
      {
        $objWidget->label = '<span class="invisible">Pflichtfeld</span>' . $arrField['label'] . '<span class="mandatory">*</span>';
      }

      $this->Template->fields .= $objWidget->parse();
      $arrFieldsForTemplate[$arrField['name']] = $objWidget->parse();
    }

    if ($this->Input->post('FORM_SUBMIT') == 'formTrackEdit' && !$this->doNotSubmit)
    {
      // Check whether there is a jumpTo page

      $arrForDatabaseUpdate = array();
      $arrForDatabaseUpdate['name'] = \Input::post('name');
      $arrForDatabaseUpdate['visibility'] = \Input::post('visibility');
      $arrForDatabaseUpdate['comment'] = \Input::post('comment');
      if ($arrForDatabaseUpdate['visibility'] == "membergroups")
      {
        $arrForDatabaseUpdate['groups'] = serialize(\Input::post('visibilityGroups'));
      }
      else
      {
        $arrForDatabaseUpdate['groups'] = null;
      }

      if (\Input::post('deleteEntry') == 'delete')
      {
        $arrForDatabaseUpdate['forDelete'] = true;
      }
      else
      {
        $arrForDatabaseUpdate['forDelete'] = false;
      }

      switch ($this->strType)
      {
        case "track":
          $this->Database->prepare("UPDATE tl_c4g_tracking_tracks %s WHERE id=?")
                     ->set($arrForDatabaseUpdate)
                     ->execute($this->intTrackId);
          break;
        case "poi":
          $this->Database->prepare("UPDATE tl_c4g_tracking_pois %s WHERE id=?")
                     ->set($arrForDatabaseUpdate)
                     ->execute($this->intPoiId);
          break;
      }



    	if (($objJumpTo = $this->objModel->getRelated('jumpTo')) !== null)
    	{
    		$this->jumpToOrReload($objJumpTo->row());
    	}

    	$this->reload();
    }

    $this->Template->fieldData = $arrFieldsForTemplate;
    $this->Template->action = "";
    $this->Template->formId = "formTrackEdit";
    $this->Template->method = "post";
    $this->Template->enctype = "";
    $this->Template->attributes = "";
    //$this->Template->novalidate = "novalidate";
    $this->Template->formSubmit = "formTrackEdit";

    $this->Template->submitTitle = "Speichern";


  }

  private function getFields($objData)
  {
    $arrFields = array();

    $arrFields['name'] = array(
      'name' => 'name',
      'label' => 'Name',
      'value' => $objData->name,
      'inputType' => 'text',
      'eval'      => array('mandatory' => true)
    );
    $arrFields['visibility'] = array
    (
  	  'name' => 'visibility',
  	  'label' => 'Sichtbarkeit',
  	  'default' => 'privat',
  	  'inputType' => 'select',
  	  'options' => array
  	  (
  	    'privat' => array('label'=>'privat','value'=>'privat'),
  	    'owngroups' => array('label'=>'eigene Gruppen','value'=>'owngroups'),
  	    'membergroups' => array('label'=>'ausgewählte Gruppen','value'=>'membergroups'),
  	    'public' => array('label'=>'öffentlich','value'=>'public')
  	  ),
  	  'eval'      => array('mandatory' => true)
    );
    if ($objData->visibility)
    {
      $arrFields['visibility']['options'][$objData->visibility]['default'] = true;
    }
    $arrGroups = array();
    if (FE_USER_LOGGED_IN)
    {
      $this->import('FrontendUser', 'User');
      $arrMemberGroups = $this->User->__get('groups');

      $arrDataGroups = array();
      if ($objData->groups)
      {
        $arrDataGroups = deserialize($objData->groups);
      }

      $objGroups = $this->Database->prepare("SELECT * FROM tl_member_group WHERE id IN(" . implode(',', $arrMemberGroups) . ")")
                                  ->execute();
      if ($objGroups->numRows > 0)
      {
        while ($objGroups->next())
        {
          $arrGroups[] = array
          (
            'label' => $objGroups->name,
            'value' => $objGroups->id,
            'default' => in_array($objGroups->id, $arrDataGroups) ? 1 : 0
          );
        }
      }
    }
    $arrFields['visibilityGroups'] = array
    (
      'name' => 'visibilityGroups',
  		'label' => 'erlaubte Gruppen',
  		'inputType' => 'checkbox',
  		'options' => $arrGroups,
 		  'eval'      => array('mandatory' => false)
    );
    $arrFields['comment'] = array
    (
      'name' => 'comment',
    	'label' => 'Kommentar',
    	'value' => $objData->comment,
    	'inputType' => 'textarea',
    	'eval'      => array('mandatory' => false)
    );
    $arrFields['deleteEntry'] = array
    (
      'name' => 'deleteEntry',
  		'label' => 'Eintrag zum löschen vormerken',
  		'inputType' => 'checkbox',
  		'options' => array
  		(
    		'delete' => array('label'=>'Eintrag zum löschen vormerken', 'value'=>'delete')
  		),
 		  'eval'      => array('mandatory' => false)
    );
    if ($objData->forDelete == 1)
    {
      $arrFields['deleteEntry']['options']['delete']['default'] = true;
    }

    return $arrFields;
  }

}
