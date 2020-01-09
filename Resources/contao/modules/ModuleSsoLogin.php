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

namespace con4gis\TrackingBundle\Resources\contao\modules;


/**
 * Class ModuleSsoLogin
 * @package c4g
 */
class ModuleSsoLogin extends \Module
{

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'mod_centralcontent';

  /**
   * Display a wildcard in the back end
   * @return string
   */
  public function generate()
  {
    if (TL_MODE == 'BE')
    {
      $objTemplate = new \BackendTemplate('be_wildcard');

      $objTemplate->wildcard = '### SSO LOGIN ###';
      $objTemplate->title = $this->headline;
      $objTemplate->id = $this->id;
      $objTemplate->link = $this->name;
      $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

      return $objTemplate->parse();
    }

    if (TL_MODE == "BE")
    {
      return '';
    }

    return parent::generate();
  }


  /**
   * Generate the module
   */
  protected function compile()
  {
    if (!\Input::get('ssoLogin'))
    {
      return;
    }

    $ssoHash = \Input::get('ssoLogin');

    $objSession = \Session::getInstance();

    if ($objSession)

      $objUser = \MemberModel::findBy('ssoHash', $ssoHash);

    if ($objUser !== null)
    {
      $time = time();

      $blnAccountError = false;

      // Check whether account is locked
      if (($objUser->locked + $GLOBALS['TL_CONFIG']['lockPeriod']) > $time)
      {
          $blnAccountError = true;
          $_SESSION['TL_ERROR'][] = sprintf($GLOBALS['TL_LANG']['ERR']['accountLocked'], ceil((($objUser->locked + $GLOBALS['TL_CONFIG']['lockPeriod']) - $time) / 60));
          $this->redirectTo403();
      }

      // Check whether account is disabled
      elseif ($objUser->disable)
      {
          $blnAccountError = true;

          $_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
          $this->log('The account has been disabled', __METHOD__, TL_ACCESS);
          $this->redirectTo403();
      }

      // Check wether login is allowed (front end only)
      elseif (!$objUser->login)
      {
          $blnAccountError = true;

          $_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
          $this->log('User "' . $objUser->username . '" is not allowed to log in', __METHOD__, TL_ACCESS);
          $this->redirectTo403();
      }

      // Check whether account is not active yet or anymore
      elseif (strlen($objUser->start) || strlen($objUser->stop))
      {
        if (strlen($objUser->start) && $objUser->start > $time)
        {
          $blnAccountError = true;

          $_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
          $this->log('The account was not active yet (activation date: ' . $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objUser->start) . ')', __METHOD__, TL_ACCESS);
          $this->redirectTo403();
        }

        if (strlen($objUser->stop) && $objUser->stop < $time)
        {
          $blnAccountError = true;

          $_SESSION['TL_ERROR'][] = $GLOBALS['TL_LANG']['ERR']['invalidLogin'];
          $this->log('The account was not active anymore (deactivation date: ' . $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objUser->stop) . ')', __METHOD__, TL_ACCESS);
          $this->redirectTo403();
        }
      }

      // Redirect to login screen if there is an error
      if ($blnAccountError)
      {
          return false;
      }

      $this->loginUser($objUser);

    }




  }

  protected function loginUser($objUser)
  {


    $strHash = sha1(session_id() . (!\Config::get('disableIpCheck') ? \Environment::get('ip') : '') . 'FE_USER_AUTH');

    // Remove old sessions
    $this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
                   ->execute((time() - \Config::get('sessionTimeout')), $strHash);

    // Insert the new session
    $this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
                   ->execute($objUser->id, time(), 'FE_USER_AUTH', session_id(), \Environment::get('ip'), $strHash);

    // Set the cookie
    $this->setCookie('FE_USER_AUTH', $strHash, (time() + \Config::get('sessionTimeout')), null, null, false, true);

    if ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null)
    {
      $strRedirect = $this->jumpToOrReload($objTarget->row());
    }
    else
    {
      $this->reload();
    }
  }

  protected function redirectTo403()
  {
    global $objPage;
    $objHandler = new $GLOBALS['TL_PTY']['error_403']();
    $objHandler->generate($objPage->id, $this->getRootPageFromUrl());
  }
}
