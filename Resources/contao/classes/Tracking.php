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
namespace con4gis\TrackingBundle\Resources\contao\classes;

use Contao\System;

/**
 * Class Tracking
 * @package c4g
 */
class Tracking extends \Controller
{

  public static function setNewPosition($strParentTable, $dblLatitude, $dblLongitude, $longAccuracy = 0, $longSpeed = 0, $timeStamp = false, $arrAdditionalData = array())
  {

    $varTime = time();
    $time = $timeStamp ? $timeStamp : $varTime;//time();

    $arrSet = array
    (
      'pTable' => $strParentTable,
      'tstamp' => $time,
      'serverTstamp' => $varTime,
      'latitude' => $dblLatitude,
      'longitude' => $dblLongitude,
      'accuracy' => $longAccuracy,
      'speed' => $longSpeed
    );

    $objDatabase = \Database::getInstance();

    $blnHasDevice = false;
    //$blnHasTrack = false;
    $intDeviceId = 0;

    if ($arrAdditionalData['imei'])
    {
      $arrSet['imei'] = $arrAdditionalData['imei'];

      $intDeviceId = self::getDeviceIdByImei($arrAdditionalData['imei']);

      if ($intDeviceId)
      {
        $arrSet['device'] = $intDeviceId;
        $blnHasDevice = true;
      }
    }

    if ($arrAdditionalData && sizeof($arrAdditionalData) > 0)
    {
      $dataForBlob = array();
      foreach ($arrAdditionalData as $key => $varValue)
      {
        if ($objDatabase->fieldExists($key, "tl_c4g_tracking_positions"))
        {
          $arrSet[$key] = $varValue;
        }
        else
        {
          $dataForBlob[$key] = $varValue;
        }
      }

      if ($dataForBlob && sizeof($dataForBlob) > 0)
      {
        $arrSet['additionalData'] = $dataForBlob;
      }

    }

    $objPosition = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPositionsModel();
    $objPosition->setRow($arrSet)->save();


    if ($blnHasDevice && $intDeviceId>0)
    {
      // UPDATE DEVICE TABLE REFERENCE
      \Database::getInstance()->prepare("UPDATE tl_c4g_tracking_devices SET lastPositionId=? WHERE id=?")
                              ->execute($objPosition->id, $intDeviceId);
    }

    if ($arrAdditionalData['trackUuid'])
    {
      // UPDATE TRACK TABLE REFERENCE
      \Database::getInstance()->prepare("UPDATE tl_c4g_tracking_tracks SET lastPositionId=? WHERE uuid=?")
          ->execute($objPosition->id, $arrAdditionalData['trackUuid']);
    }

    return $objPosition->id;

  }

  public static function setNewDevicePositions()
  {
    echo self::setNewPosition("test", 12, 12, 0, 0, false, array('pid' => 'dfdf', 'test' => 'dfgdfg'));
  }

  public static function setNewPoi($intConfiguration, $intMemberId, $strVisibility = "privat", $strName = "", $intTrackUuid = 0, $timeStamp, $arrPositionData = array())
  {

    $strUuid = uniqid('', true);
    $timeStamp = $timeStamp ? $timeStamp : time();

    // Save Position into position-table
    $intPositionId = self::setNewPosition("pois", $arrPositionData['latitude'], $arrPositionData['longitude'], $arrPositionData['accuracy'], $arrPositionData['speed'], $timeStamp, $arrPositionData['additionalData']);

    $arrSet = array
    (
      'tstamp' => $timeStamp,
      'pid' => $intConfiguration,
      'uuid' => $strUuid,
      'member' => $intMemberId,
      'name' => $strName,
      'visibility' => $strVisibility,
      'trackUuid' => $intTrackUuid
    );

    $arrSet['positionId'] = $intPositionId;

    if ($arrPositionData['additionalData']['imei'])
    {

      $intDeviceId = self::getDeviceIdByImei($arrPositionData['additionalData']['imei']);

      if ($intDeviceId)
      {
        $arrSet['device'] = $intDeviceId;
      }
    }

    $objPoi = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel();
    $objPoi->setRow($arrSet)->save();


    $arrTrackingPoi = array();
    $arrTrackingPoi['poiId'] = $objPoi->id;
    $arrTrackingPoi['poiUuid'] = $strUuid;

    $packages = System::getContainer()->getParameter('kernel.packages');
    $arrTrackingPoi['version'] = $packages['con4gis/tracking'];

    return $arrTrackingPoi;

  }

  public static function setNewTrack($intConfiguration, $intMemberId, $strVisibility = "privat", $strName = "", $timeStamp, $arrPositionData = array())
  {

    $timeStamp = $timeStamp ? $timeStamp : time();
    $strUuid = uniqid('', true);

    $arrPositionData['additionalData']['trackUuid'] = $strUuid;

    $arrSet = array
    (
      'tstamp' => $timeStamp,
      'pid' => $intConfiguration,
      'uuid' => $strUuid,
      'member' => $intMemberId,
      'name' => $strName,
      'visibility' => $strVisibility
    );
    if ($arrPositionData['latitude'] && $arrPositionData['longitude'])
    {
      $intPositionId = self::setNewPosition("tracks", $arrPositionData['latitude'], $arrPositionData['longitude'], $arrPositionData['accuracy'], $arrPositionData['speed'], $timeStamp, $arrPositionData['additionalData']);
      $arrSet['firstPositionId'] = $intPositionId;
      $arrSet['lastPositionId'] = $intPositionId;
    }

    if ($arrPositionData['imei'])
    {
      $arrSet['imei'] = $arrPositionData['imei'];
    }

    $objTrack = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel();
    $objTrack->setRow($arrSet)->save();

    $arrTrackingTrack['trackId'] = $objTrack->id;
    $arrTrackingTrack['trackUuid'] = $strUuid;

    $packages = System::getContainer()->getParameter('kernel.packages');
    $arrTrackingTrack['version'] = $packages['con4gis/tracking'];

    return $arrTrackingTrack;

  }

  public static function getTrackingConfig()
  {
    $objRootPage = \Frontend::getRootPageFromUrl();

    $arrTrackingConfig = array
    (
      'hasTrackingConfiguration' => false
    );

    if ($objRootPage->c4gtracking_configuration)
    {


      $objTrackingConfiguration = $objRootPage->getRelated('c4gtracking_configuration');

      if ($objTrackingConfiguration !== null)
      {
        $arrTrackingConfig['hasTrackingConfiguration'] = true;

        $arrTrackingInformation = $objTrackingConfiguration->row();

        foreach ($arrTrackingInformation as $key => $value)
        {
          $arrTrackingConfig[$key] = self::manipulateTrackingInfo($key, $value);

          if (is_array(deserialize($arrTrackingConfig[$key])))
          {
            $arrTrackingConfig[$key] = deserialize($arrTrackingConfig[$key]);
          }

        }

      }
      else
      {
        $arrTrackingConfig['message'] = "no tracking configuration";
      }

    }
    else
    {
      $arrTrackingConfig['message'] = "no tracking configuration";
    }

    $packages = System::getContainer()->getParameter('kernel.packages');
    $arrTrackingConfig['version'] = $packages['con4gis/tracking'];

    return $arrTrackingConfig;

  }

  private static function getDeviceIdByImei($strImei)
  {

    $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findByImeiEndpiece($strImei);

    if ($objDevice !== null)
    {
      return $objDevice->id;
    }

    return false;

  }

  public static function getIgnitionStatus($indDeviceId)
  {
    // 12 = Zündung an
    // 13 = Zündung aus
    $objDatabase = \Database::getInstance();
    $objIgnitionInfo = $objDatabase->prepare("SELECT boxStatus FROM tl_c4g_tracking_positions WHERE device=? AND boxStatus >= 12 AND boxStatus <= 13 ORDER BY tstamp DESC")
                                   ->limit(1)
                                   ->execute($indDeviceId);
    if ($objIgnitionInfo->numRows)
    {
      return $objIgnitionInfo->boxStatus == 12;
    }
    return null;
  }

  private static function manipulateTrackingInfo($strKey, $strValue)
  {
    switch ($strKey)
    {
      case "tstamp":
        //$objDate = new \Date($strValue);
        //$strValue = $objDate->datim;
        break;
    }
    return $strValue;
  }

  public static function sendPushNotificationByToken($intConfiguraion, $strType, $strToken, $strContent)
  {

    $objTracking = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingModel::findBy('id', $intConfiguraion);
    if ($objTracking === null)
    {
      return;
    }

    switch ($strType)
    {
      case "android":
        self::sendGoogleCloudMessage($strToken, $strContent, $objTracking);
        break;
    }

  }

  private static function sendGoogleCloudMessage($strToken, $strContent, $objTracking)
  {
    $strGoogleApiKey = $objTracking->pushGcmApiKey;
    $strGoogleGcmUrl = 'https://gcm-http.googleapis.com/gcm/send';


    $arrGcmHeaders = array
    (
      'Authorization: key=' . $strGoogleApiKey,
      'Content-Type: application/json'
    );

    $arrGcmFields = array
    (
      'to' => array
      (
        $strToken
      ),
      'data' => array
      (
        'message' => $strContent
      )
    );

    // Open connection
    $ch = curl_init();

    // Set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $strGoogleGcmUrl);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrGcmHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Disabling SSL Certificate support temporarly
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrGcmFields));

    // Execute post
    $result = curl_exec($ch);
    if ($result === FALSE)
    {
        $logger = \System::getContainer()->get('logger');
        $logger->error("Failed while sending push message via curl.");
      //CakeLog::write('log','Gc,notofication failed. Id:' . $id . '; Msg: ' . curl_error($ch));
      //die('Curl failed: ' . curl_error($ch));
    }

    // Close connection
    curl_close($ch);
  }

}