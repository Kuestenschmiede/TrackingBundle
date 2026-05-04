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
namespace con4gis\TrackingBundle\Classes;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\MapsBundle\Resources\contao\models\C4gMapSettingsModel;
use Contao\Controller;
use Contao\Database;
use Contao\Frontend;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class Tracking
 * @package c4g
 */
class Tracking extends Controller
{
    public static function setNewPosition($strParentTable, $dblLatitude, $dblLongitude, $longAccuracy = 0, $longSpeed = 0, $timeStamp = false, $arrAdditionalData = [])
    {
        $varTime = time();
        $time = $timeStamp ? $timeStamp : $varTime;//time();

        $arrSet = [
          'pTable' => $strParentTable,
          'tstamp' => $time,
          'serverTstamp' => $varTime,
          'latitude' => $dblLatitude,
          'longitude' => $dblLongitude,
          'accuracy' => $longAccuracy,
          'speed' => $longSpeed,
        ];

        $objDatabase = Database::getInstance();

        $blnHasDevice = false;
        //$blnHasTrack = false;
        $intDeviceId = 0;

        if ($arrAdditionalData['imei']) {
            $arrSet['imei'] = $arrAdditionalData['imei'];

            $intDeviceId = self::getDeviceIdByImei($arrAdditionalData['imei']);

            if ($intDeviceId) {
                $arrSet['device'] = $intDeviceId;
                $blnHasDevice = true;
            }
        }

        if ($arrAdditionalData && sizeof($arrAdditionalData) > 0) {
            $dataForBlob = [];
            foreach ($arrAdditionalData as $key => $varValue) {
                if ($objDatabase->fieldExists($key, 'tl_c4g_tracking_positions')) {
                    $arrSet[$key] = $varValue;
                } else {
                    $dataForBlob[$key] = $varValue;
                }
            }

            if ($dataForBlob && sizeof($dataForBlob) > 0) {
                $arrSet['additionalData'] = $dataForBlob;
            }
        }

        // address translation
        //
        try {
            // number of decimals we can shorten to while still having good enough precision
            $decimals = 6;

            $lookupLat = $arrSet['latitude'];
            $lookupLon = $arrSet['longitude'];

            $cachedAddress = self::getStoredAddress($lookupLat, $lookupLon);

            if ($cachedAddress !== "") {
                $arrSet['address'] = $cachedAddress;
            } else {
                $address = self::reverseGeocode([$arrSet['longitude'], $arrSet['latitude']], true);

                if ($address && is_array($address)) {
                    $strAddress =  '';
                    $strAddress .= array_key_exists('street', $address) ? $address['street'] : "";
                    $strAddress .= array_key_exists('housenumber', $address) ? " " . $address['housenumber'] : "";

                    $strAddress .= array_key_exists('postalcode', $address) ? ", " . $address['postalcode'] : "";
                    $strAddress .= array_key_exists('localadmin', $address) ? " " . $address['localadmin'] : "";

                    $arrSet['address'] = $strAddress;

                    $insertSql = "INSERT INTO tl_c4g_tracking_addresses %s";
                    $params = [
                        'tstamp' => time(),
                        'latitude' => $arrSet['latitude'],
                        'longitude' => $arrSet['longitude'],
                        'address' => $strAddress
                    ];
                    Database::getInstance()->prepare($insertSql)
                        ->set($params)->execute();
                } else {
                    $arrSet['address'] = "";
                }
            }

        } catch (\Throwable $exception) {
            C4gLogModel::addLogEntry("tracking", $exception->getMessage());
            $arrSet['address'] = "";
        }

        $objPosition = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPositionsModel();
        $objPosition->setRow($arrSet)->save();

        if ($blnHasDevice && $intDeviceId > 0) {
            // UPDATE DEVICE TABLE REFERENCE
            Database::getInstance()->prepare('UPDATE tl_c4g_tracking_devices SET lastPositionId=? WHERE id=?')
                              ->execute($objPosition->id, $intDeviceId);
        }

        if ($arrAdditionalData['trackUuid']) {
            // UPDATE TRACK TABLE REFERENCE
            Database::getInstance()->prepare('UPDATE tl_c4g_tracking_tracks SET lastPositionId=? WHERE uuid=?')
          ->execute($objPosition->id, $arrAdditionalData['trackUuid']);
        }

        return $objPosition->id;
    }

    private static function getStoredAddress($lat, $lon)
    {
        try {
            $sql = "SELECT address FROM tl_c4g_tracking_addresses WHERE latitude = ? AND longitude = ?";
            $storedAddress = Database::getInstance()
                ->prepare($sql)
                ->execute($lat, $lon)
                ->fetchAssoc();

            if ($storedAddress) {
                return $storedAddress['address'];
            }

            return "";
        } catch (\Throwable $exception) {
            C4gLogModel::addLogEntry("tracking", $exception->getMessage());
            return "";
        }
    }

    private static function reverseGeocode($coordinates, $getArray)
    {
        try {
            $settings = C4gMapSettingsModel::findOnly();
            if ($settings->con4gisIoUrl && $settings->con4gisIoKey) {
                $searchUrl = rtrim($settings->con4gisIoUrl, '/') . '/';
                $searchUrl .= 'reverse.php?key=' . $settings->con4gisIoKey;
                $searchUrl .= '&lon=' . $coordinates[0] . '&lat=' . $coordinates[1] . '&format=json';

                $headers = [];
                if (array_key_exists('HTTP_REFERER', $_SERVER) && $_SERVER['HTTP_REFERER']) {
                    $headers['Referer'] = $_SERVER['HTTP_REFERER'];
                }
                if (array_key_exists('HTTP_USER_AGENT', $_SERVER) && $_SERVER['HTTP_USER_AGENT']) {
                    $headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
                }
                $client = HttpClient::create([
                    'headers' => $headers,
                ]);
                try {
                    $response = $client->request('GET', $searchUrl, ['timeout' => 2]);
                    $statusCode = $response->getStatusCode();
                    if (($statusCode === 200)) {
                        $response = $response->getContent();
                        $response = \GuzzleHttp\json_decode($response, true);

                        if ($getArray) {
                            $result = $response['address'];
                        } else {
                            $result = $response['display_name'];
                        }

                        return $result;
                    }
                } catch (\Throwable $exception) {
                    C4gLogModel::addLogEntry("tracking", $exception->getMessage());
                    return "";
                }
            }

            return "";
        } catch(\Throwable $exception) {
            C4gLogModel::addLogEntry("tracking", $exception->getMessage());
            return "";
        }
    }

    public static function setNewDevicePositions()
    {
        echo self::setNewPosition('test', 12, 12, 0, 0, false, ['pid' => 'dfdf', 'test' => 'dfgdfg']);
    }

    public static function setNewPoi($intConfiguration, $intMemberId, $strVisibility = 'privat', $strName = '', $intTrackUuid = 0, $timeStamp = 0, $arrPositionData = [])
    {
        $strUuid = uniqid('', true);
        $timeStamp = $timeStamp ? $timeStamp : time();

        // Save Position into position-table
        $intPositionId = self::setNewPosition('pois', $arrPositionData['latitude'], $arrPositionData['longitude'], $arrPositionData['accuracy'], $arrPositionData['speed'], $timeStamp, $arrPositionData['additionalData']);

        $arrSet = [
      'tstamp' => $timeStamp,
      'pid' => $intConfiguration,
      'uuid' => $strUuid,
      'member' => $intMemberId,
      'name' => $strName,
      'visibility' => $strVisibility,
      'trackUuid' => $intTrackUuid,
    ];

        $arrSet['positionId'] = $intPositionId;

        if ($arrPositionData['additionalData']['imei']) {
            $intDeviceId = self::getDeviceIdByImei($arrPositionData['additionalData']['imei']);

            if ($intDeviceId) {
                $arrSet['device'] = $intDeviceId;
            }
        }

        $objPoi = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel();
        $objPoi->setRow($arrSet)->save();

        $arrTrackingPoi = [];
        $arrTrackingPoi['poiId'] = $objPoi->id;
        $arrTrackingPoi['poiUuid'] = $strUuid;

        $packages = System::getContainer()->getParameter('kernel.packages');
        $arrTrackingPoi['version'] = $packages['con4gis/tracking'];

        return $arrTrackingPoi;
    }

    public static function setNewTrack($intConfiguration, $intMemberId, $strVisibility = 'privat', $strName = '', $timeStamp, $arrPositionData = [])
    {
        $timeStamp = $timeStamp ? $timeStamp : time();
        $strUuid = uniqid('', true);

        $arrPositionData['additionalData']['trackUuid'] = $strUuid;

        $arrSet = [
      'tstamp' => $timeStamp,
      'pid' => $intConfiguration,
      'uuid' => $strUuid,
      'member' => $intMemberId,
      'name' => $strName,
      'visibility' => $strVisibility,
    ];
        if ($arrPositionData['latitude'] && $arrPositionData['longitude']) {
            $intPositionId = self::setNewPosition('tracks', $arrPositionData['latitude'], $arrPositionData['longitude'], $arrPositionData['accuracy'], $arrPositionData['speed'], $timeStamp, $arrPositionData['additionalData']);
            $arrSet['firstPositionId'] = $intPositionId;
            $arrSet['lastPositionId'] = $intPositionId;
        }

        if ($arrPositionData['imei']) {
            $arrSet['imei'] = $arrPositionData['imei'];
        }

        $objTrack = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel();
        $objTrack->setRow($arrSet)->save();

        $arrTrackingTrack['trackId'] = $objTrack->id;
        $arrTrackingTrack['trackUuid'] = $strUuid;

        $packages = System::getContainer()->getParameter('kernel.bundles');
        $arrTrackingTrack['version'] = $packages['con4gis/tracking'];

        return $arrTrackingTrack;
    }

    public static function getTrackingConfig()
    {
        $objRootPage = Frontend::getRootPageFromUrl();

        $arrTrackingConfig = [
      'hasTrackingConfiguration' => false,
    ];

        if ($objRootPage->c4gtracking_configuration) {
            $objTrackingConfiguration = $objRootPage->getRelated('c4gtracking_configuration');

            if ($objTrackingConfiguration !== null) {
                $arrTrackingConfig['hasTrackingConfiguration'] = true;

                $arrTrackingInformation = $objTrackingConfiguration->row();

                foreach ($arrTrackingInformation as $key => $value) {
                    $arrTrackingConfig[$key] = self::manipulateTrackingInfo($key, $value);

                    if (is_array(StringUtil::deserialize($arrTrackingConfig[$key]))) {
                        $arrTrackingConfig[$key] = StringUtil::deserialize($arrTrackingConfig[$key]);
                    }
                }
            } else {
                $arrTrackingConfig['message'] = 'no tracking configuration';
            }
        } else {
            $arrTrackingConfig['message'] = 'no tracking configuration';
        }

        $packages = System::getContainer()->getParameter('kernel.bundles');
        $arrTrackingConfig['version'] = $packages['con4gis/tracking'];

        return $arrTrackingConfig;
    }

    private static function getDeviceIdByImei($strImei)
    {
        $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findByImeiEndpiece($strImei);

        if ($objDevice !== null) {
            return $objDevice->id;
        }

        return false;
    }

    public static function getIgnitionStatus($indDeviceId)
    {
        // 12 = Zündung an
        // 13 = Zündung aus
        $objDatabase = Database::getInstance();
        $objIgnitionInfo = $objDatabase->prepare('SELECT boxStatus FROM tl_c4g_tracking_positions WHERE device=? AND boxStatus >= 12 AND boxStatus <= 13 ORDER BY tstamp DESC')
                                   ->limit(1)
                                   ->execute($indDeviceId);
        if ($objIgnitionInfo->numRows) {
            return $objIgnitionInfo->boxStatus == 12;
        }

        return null;
    }

    private static function manipulateTrackingInfo($strKey, $strValue)
    {
        switch ($strKey) {
      case 'tstamp':
        //$objDate = new \Date($strValue);
        //$strValue = $objDate->datim;
        break;
    }

        return $strValue;
    }

    public static function sendPushNotificationByToken($intConfiguraion, $strType, $strToken, $strContent)
    {
        $objTracking = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingModel::findBy('id', $intConfiguraion);
        if ($objTracking === null) {
            return;
        }

        switch ($strType) {
      case 'android':
        self::sendGoogleCloudMessage($strToken, $strContent, $objTracking);

        break;
    }
    }

    private static function sendGoogleCloudMessage($strToken, $strContent, $objTracking)
    {
        $strGoogleApiKey = $objTracking->pushGcmApiKey;
        $strGoogleGcmUrl = 'https://gcm-http.googleapis.com/gcm/send';

        $arrGcmHeaders = [
      'Authorization: key=' . $strGoogleApiKey,
      'Content-Type: application/json',
    ];

        $arrGcmFields = [
      'to' => [
        $strToken,
      ],
      'data' => [
        'message' => $strContent,
      ],
    ];

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
        if ($result === false) {
            $logger = System::getContainer()->get('logger');
            $logger->error('Failed while sending push message via curl.');
            //CakeLog::write('log','Gc,notofication failed. Id:' . $id . '; Msg: ' . curl_error($ch));
      //die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
    }
}
