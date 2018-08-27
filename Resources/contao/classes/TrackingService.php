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

namespace con4gis\TrackingBundle\Resources\contao\classes;


/**
 * Class TrackingService
 * @package c4g
 */
class TrackingService extends \Controller
{

    private $arrReturn = array();
    private $blnDebugMode = false;

    public function __construct()
    {
        if ($this->Input->get('debug') && ($this->Input->get('debug')=='1' || $this->Input->get('debug')=='true'))
        {
            $this->blnDebugMode = true;
        }
    }

    public function generate($method)
    {
        \System::loadLanguageFile('tl_c4g_tracking');

        $strMethod = 'tracking' . ucfirst(\Input::get('method'));
        $method = 'tracking' . ucfirst($method);
        if (method_exists($this, $method)) {
            if ($this->$method()) {
                return $this->arrReturn;
            }
            return $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['method_error'] . $strMethod);
        } elseif (method_exists($this, $strMethod)) {
            if ($this->$strMethod()) {
                return $this->arrReturn;
            }
            return $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['method_error'] . $strMethod);
        } else {
            return false;
        }
    }

    private function trackingGetLive()
    {

        if (\Input::get('maps'))
        {
            $intMapsItem = \Input::get('maps');
        }

        $this->import('Database');
        $time = time();
        $strTimeSelect = $time - (60*60);
        //$strTimeSelect = 0;

        /*$objPositions = $this->Database->prepare("SELECT * FROM (SELECT tl_c4g_tracking_positions.*, tl_c4g_tracking_tracks.name, tl_c4g_tracking_tracks.comment, tl_c4g_tracking_tracks.visibility FROM tl_c4g_tracking_positions  LEFT JOIN tl_c4g_tracking_tracks ON tl_c4g_tracking_positions.track_uuid=tl_c4g_tracking_tracks.uuid WHERE tl_c4g_tracking_positions.tstamp>? ORDER BY tl_c4g_tracking_positions.tstamp DESC) as inv GROUP BY track_uuid")
                                               ->execute($strTimeSelect);*/

        if (\Input::get('id'))
        {

            if (is_array(\Input::get('id')))
            {
                // multiple devices
                $arrDevices = \Input::get('id');

                $arrIds = implode(',', array_map('intval', $arrDevices));

                $objPositions = $this->Database->prepare("SELECT tl_c4g_tracking_devices.name, tl_c4g_tracking_positions.* FROM tl_c4g_tracking_devices LEFT JOIN tl_c4g_tracking_positions ON tl_c4g_tracking_devices.lastPositionId=tl_c4g_tracking_positions.id WHERE tl_c4g_tracking_devices.lastPositionId>0 AND tl_c4g_tracking_devices.id IN (" . $arrIds . ")")
                  ->execute();
            }
            else
            {
                // single devices
                $intDeviceId = \Input::get('id');
                $objPositions = $this->Database->prepare("SELECT tl_c4g_tracking_devices.name, tl_c4g_tracking_positions.* FROM tl_c4g_tracking_devices LEFT JOIN tl_c4g_tracking_positions ON tl_c4g_tracking_devices.lastPositionId=tl_c4g_tracking_positions.id WHERE tl_c4g_tracking_devices.lastPositionId>0 AND tl_c4g_tracking_devices.id=?")
                  ->execute($intDeviceId);
            }
        }
        elseif (\Input::get('useGroup'))
        {
            $intGroupId = \Input::get('useGroup');
            $objPositions = $this->Database->prepare("SELECT tl_c4g_tracking_devices.name, tl_c4g_tracking_positions.* FROM tl_c4g_tracking_devices LEFT JOIN tl_c4g_tracking_positions ON tl_c4g_tracking_devices.lastPositionId=tl_c4g_tracking_positions.id WHERE tl_c4g_tracking_devices.lastPositionId>0 AND tl_c4g_tracking_devices.groupId=?")
                                            ->execute($intGroupId);
        }
        else
        {
            // Fallback: keine weiteren Einstellungen -> alle Geräte mit Positionsdaten
            $objPositions = $this->Database->prepare("SELECT tl_c4g_tracking_devices.name, tl_c4g_tracking_positions.* FROM tl_c4g_tracking_devices LEFT JOIN tl_c4g_tracking_positions ON tl_c4g_tracking_devices.lastPositionId=tl_c4g_tracking_positions.id WHERE tl_c4g_tracking_devices.lastPositionId>0")
              ->execute();
        }


        if ($objPositions->numRows) {

            $arrFeatures = array();
            while ($objPositions->next())
            {

                $arrFeatures[] = array
                (
                    'type' => 'Feature',
                    'properties' => array
                    (
                        'name' => $objPositions->name ? $objPositions->name : $objPositions->comment,
                        'positionId' => $objPositions->id,
                        'popup' => array(
                            'content' => 'devices:live;id,' . $objPositions->id . ';maps,' . $intMapsItem
                        )
                    ),
                    'geometry' => array
                    (
                        'type' => 'Point',
                        'coordinates' => array
                        (
                            (float) $objPositions->longitude,
                            (float) $objPositions->latitude
                        )
                    )
                );
                // Todo: alle Daten im properties-objekt bereit stellen
            }
            $arrReturn = array();
            $arrReturn['type'] = "FeatureCollection";
            $arrReturn['features'] = $arrFeatures;

            $this->arrReturn = $arrReturn;
            return true;
        }
        $this->arrReturn = array
        (
            'type' => 'Feature'
        );
        return true;
    }

    private function trackingGetBoxTrack()
    {


        $blnUseFromFilter = false;
        $blnUseToFilter = false;

        $this->import('Database');
        $varBoxId = \Input::get('id');

        if (!is_array($varBoxId))
        {
            $varBoxId = array(
                0 => $varBoxId
            );
        }



        if (\Input::get('filterFrom'))
        {
            $blnUseFromFilter = true;
            $strFromFilter = \Input::get('filterFrom');
        }
        if (\Input::get('filterTo'))
        {
            $blnUseToFilter = true;
            $strToFilter = \Input::get('filterTo');
        }
        //filterFrom=1421017200&filterTo=1434060000

        $arrFeatures = array();

        foreach ($varBoxId as $intBoxId)
        {
            //echo $intBoxId;
            $arrCoordinates = array();

            $strAdditionalWhere = "";

            $arrParams = array();

            $arrParams[] = $intBoxId;

            $blnUseTimeZoneSettings = false;
            $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findBy('id', $intBoxId);
            if ($objDevice !== null)
            {
                $arrDeviceData = $objDevice->row();

                if ($arrDeviceData['timeZone'] && $arrDeviceData['timeZone']!=\Config::get('timeZone'))
                {
                    $blnUseTimeZoneSettings = true;
                    $strTimeZoneSettings = $arrDeviceData['timeZone'];
                    // store local and device timezone
                    $dateTimeZoneDevice = new \DateTimeZone($strTimeZoneSettings);
                    $dateTimeZoneServer = new \DateTimeZone(\Config::get('timeZone'));

                    // get one date-time-object for device timezone
                    $dateTimeDevice = new \DateTime("now", $dateTimeZoneDevice);

                    // get the offset of the timezone
                    $timeOffset = $dateTimeZoneServer->getOffset($dateTimeDevice);

                }
            }

            if ($blnUseFromFilter)
            {
                $strAdditionalWhere .= " AND tstamp>?";
                if ($blnUseTimeZoneSettings) {
                    $arrParams[] = $strFromFilter - $timeOffset;
                } else {
                    $arrParams[] = $strFromFilter;
                }
            }

            if ($blnUseToFilter)
            {
                $strAdditionalWhere .= " AND tstamp<?";
                if ($blnUseTimeZoneSettings) {
                    $arrParams[] = $strToFilter - $timeOffset;
                } else {
                    $arrParams[] = $strToFilter;
                }
            }


            $objPositions = $this->Database->prepare("SELECT * FROM tl_c4g_tracking_positions WHERE device=?" . $strAdditionalWhere . " ORDER BY tstamp")
                                           ->execute($arrParams);

            if ($objPositions->numRows)
            {
                $arrPositionInfo = array();
                while ($objPositions->next())
                {
                    $arrCoordinates[] = array
                    (
                        (float) $objPositions->longitude,
                        (float) $objPositions->latitude
                    );

                    if ($blnUseTimeZoneSettings)
                    {

                        // recalculate timestamp with given offset
                        $varTimeStamp = $objPositions->tstamp + $timeOffset;

                        $arrPositionInfo[] = \Date::parse(\Config::get('datimFormat'), $varTimeStamp);
                        $strPositionInfo = \Date::parse(\Config::get('datimFormat'), $varTimeStamp);

                    }
                    else
                    {
                        $arrPositionInfo[] = \Date::parse(\Config::get('datimFormat'), $objPositions->tstamp);
                        $strPositionInfo = \Date::parse(\Config::get('datimFormat'), $objPositions->tstamp);
                    }
                    $arrFeatures[] = array
                    (
                        "type" => "Feature",
                        "geometry" => array(
                            "type" => "Point",
                            "coordinates" => array(
                                (float) $objPositions->longitude,
                                (float) $objPositions->latitude
                            )
                        ),
                        'properties' => array
                        (
                            'projection' => 'EPSG:4326',
                            'tooltip' => $strPositionInfo
                        )
                    );

                }

                $arrGeometry = array();
                $arrGeometry['type'] = 'LineString';
                $arrGeometry['coordinates'] = $arrCoordinates;


                $arrFeatures[] = array
                (
                    'type' => 'Feature',
                    'geometry' => $arrGeometry,
                    'properties' => array
                    (
                        'projection' => 'EPSG:4326',
                        'positioninfos' => $arrPositionInfo,
                        //'tooltip' => 'Test'
                    )
                );

            }

        }

        $arrReturn = array(
            'type' => 'FeatureCollection',
            'features' => $arrFeatures
        );


        $this->arrReturn = $arrReturn;
        return true;

    }

    private function trackingGetTrack()
    {
        $this->import('Database');
        $arrCoordinates = array();

        $trackId = \Input::get('id');

        $objPositions = $this->Database->prepare("SELECT * FROM tl_c4g_tracking_positions WHERE trackUuid=?")
                                       ->execute($trackId);

        if ($objPositions->numRows)
        {
          while ($objPositions->next())
          {
            $arrCoordinates[] = array
            (
                (float) $objPositions->longitude,
                (float) $objPositions->latitude
            );
          }
        }

        $arrGeometry = array();
        $arrGeometry['type'] = 'LineString';
        $arrGeometry['coordinates'] = $arrCoordinates;

        $arrFeatures = array();
        $arrFeatures[] = array
        (
            'type' => 'Feature',
            'geometry' => $arrGeometry,
            'properties' => array
            (
                'projection' => 'EPSG:4326'
            )
        );
        // Todo: alle Daten im properties-objekt bereit stellen

        $objPois = $this->Database->prepare("SELECT * FROM tl_c4g_tracking_pois WHERE trackUuid=?")
                                       ->execute($trackId);
        if ($objPois->numRows > 0)
        {
          while ($objPois->next())
          {
            $arrFeatures[] = array
            (
                'type' => 'Feature',
                'properties' => array
                (
                  'name' => $objPois->name ? $objPois->name : $objPois->comment
                ),
                'geometry' => array
                (
                    'type' => 'Point',
                    'coordinates' => array
                    (
                        (float) $objPois->longitude,
                        (float) $objPois->latitude
                    )
                )
            );
              // Todo: alle Daten im properties-objekt bereit stellen
          }
        }

        $arrReturn = array();
        $arrReturn['type'] = "FeatureCollection";
        $arrReturn['features'] = $arrFeatures;

        $this->arrReturn = $arrReturn;
        return true;
    }


    private function trackingNewPoi()
    {

        $arrPositionData = array();

        if ($this->blnDebugMode)
        {
            \Input::setPost('user',\Input::get('user'));
            \Input::setPost('configuration',\Input::get('configuration'));
            \Input::setPost('latitude',\Input::get('latitude'));
            \Input::setPost('longitude',\Input::get('longitude'));
            \Input::setPost('trackid',\Input::get('trackid'));
            \Input::setPost('accuracy',\Input::get('accuracy'));
            \Input::setPost('speed',\Input::get('speed'));
            \Input::setPost('imei',\Input::get('imei'));
        }

        $blnHasError = false;
        if (!\Input::post('user'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_username']);
            $blnHasError = true;
        }
        if (!\Input::post('configuration'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_config']);
            $blnHasError = true;
        }
        if (!\Input::post('latitude'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_latitude'] );
            $blnHasError = true;
        }
        if (!\Input::post('longitude'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_longitude']);
            $blnHasError = true;
        }

        $strName = "";
        if (\Input::post('name'))
        {
            $strName = \Input::post('name');
        }

        $intTrackId = 0;
        if (\Input::post('trackid'))
        {
            $intTrackId = \Input::post('trackid');
        }

        if (!$blnHasError)
        {

            $arrPositionData['latitude'] = \Input::post('latitude');
            $arrPositionData['longitude'] = \Input::post('longitude');

            // optional data
            $timeStamp = false;
            $arrAdditionalData = array();

            if (\Input::post('accuracy'))
            {
                $arrPositionData['accuracy'] = \Input::post('accuracy');
            }
            if (\Input::post('speed'))
            {
                $arrPositionData['speed'] = \Input::post('speed');
            }
            if (\Input::post('timestamp'))
            {
                $timeStamp = \Input::post('timestamp');
            }

            if (\Input::post('positiontype'))
            {
                $arrAdditionalData['positiontype'] = \Input::post('positiontype');
            }
            if (\Input::post('imei'))
            {
                $arrAdditionalData['imei'] = \Input::post('imei');
            }
            if (\Input::post('batterystatus'))
            {
                $arrAdditionalData['batterystatus'] = \Input::post('batterystatus');
            }
            if (\Input::post('networkinfo'))
            {
                $arrAdditionalData['networkinfo'] = \Input::post('networkinfo');
            }

            $arrPositionData['additionalData'] = $arrAdditionalData;

            $this->arrReturn['error'] = false;

            $this->arrReturn['track'] = Tracking::setNewPoi(\Input::post('configuration'), \Input::post('user'), (\Input::post('privacy') ? \Input::post('privacy') : "privat"), $strName, $intTrackId, $timeStamp, $arrPositionData);


        }

        return true;
    }

    private function trackingGetDeviceStatus()
    {
        if ($this->blnDebugMode)
        {
            $arrParams = array('api_key', 'imei');

            foreach ($arrParams as $strParam)
            {
                if (\Input::get($strParam))
                {
                    \Input::setPost($strParam, \Input::get($strParam));
                }
            }
        }

        // check mandatory params
        if (!\Input::post('api_key') || !\Input::post('imei'))
        {
            return false;
        }

        // check api_key
        $objTracking = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingModel::findBy('apiKey', \Input::post('api_key'));
        if ($objTracking === null)
        {
            return false;
        }

        // check imei number
        $objTrackingBox = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findByImeiEndpiece(\Input::post('imei'));
        if ($objTrackingBox === null)
        {
            $this->arrReturn = $this->getErrorReturn(array
            (
                "message" => "Device not found",
                "status" => 900
            ));
            return true;
        }

        if ($objTrackingBox->deaktivated)
        {
            $this->arrReturn['status'] = 0;
        }
        else
        {
            $this->arrReturn['status'] = 1;
        }

        return true;

    }

    private function trackingGetRegisteredBoxes()
    {
        if ($this->blnDebugMode)
        {
            $arrParams = array('api_key');

            foreach ($arrParams as $strParam)
            {
                if (\Input::get($strParam))
                {
                    \Input::setPost($strParam, \Input::get($strParam));
                }
            }
        }

        // check mandatory params
        if (!\Input::post('api_key'))
        {
            return false;
        }

        // check api_key
        $objTracking = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingModel::findBy('apiKey', \Input::post('api_key'));
        if ($objTracking === null)
        {
            return false;
        }

        $objTrackingBoxes = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findBy('type','box');

        $arrDevices = array();

        if ($objTrackingBoxes!==null)
        {
            while($objTrackingBoxes->next())
            {
                if (!$objTrackingBoxes->imei) continue;
                $arrDevices[] = $objTrackingBoxes->imei;
            }
        }


        $this->arrReturn['error'] = false;
        $this->arrReturn['devices'] = $arrDevices;

        return true;

    }

    private function trackingNewPositionFromBox()
    {

        if ($this->blnDebugMode)
        {
            $arrParams = array('api_key','date','imei','latitude','longitude','phoneNo','speed','mileage','driverId','temperature','status');

            foreach ($arrParams as $strParam)
            {
                if (\Input::get($strParam))
                {
                    \Input::setPost($strParam, \Input::get($strParam));
                }
            }
        }

        // check mandatory params
        if (!\Input::post('api_key') || !\Input::post('date') || !\Input::post('imei') || !\Input::post('latitude') || !\Input::post('longitude'))
        {
            return false;
        }

        // check api_key
        $objTracking = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingModel::findBy('apiKey', \Input::post('api_key'));
        if ($objTracking === null)
        {
            return false;
        }

        // check imei number
        $objTrackingBox = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findByImeiEndpiece(\Input::post('imei'));
        if ($objTrackingBox === null)
        {
            $this->arrReturn = $this->getErrorReturn(array
            (
                "message" => "Device not found",
                "status" => 900
            ));
            return true;
        }

        if ($objTrackingBox->deaktivated)
        {
            $this->arrReturn = $this->getErrorReturn(array
            (
                "message" => "Device is deaktivated",
                "status" => 901
            ));
            return true;
        }
        # Berechnung des Akku Status der tracking Devices
        if (\Contao\Validator::isNumeric(\Input::post('akku')))  {
            $deviceId = \Input::post('device_id');
            if ( (str_split($deviceId)[1] == '4') | (str_split($deviceId)[1] == '3') ) {
                $akku = number_format(((100 / 254) * round((\Input::post('akku') * 1000) / 19.8)) / 100, 4, '.', '');
            }
            elseif ( (str_split($deviceId)[1] == '2') | (str_split($deviceId)[1] == '1')) {
                $akku = number_format((100/255) * round(((\Input::post('akku') - 3.4) / 0.88) *255) / 100, 4, '.','');
            }
        } else {
            $akku = '';
        }

        $arrAdditionalData = array(
            'boxPhoneNo' => \Input::post('phoneNo') ? \Input::post('phoneNo') : '',
            'boxMileage' => \Input::post('mileage') ? \Input::post('mileage') : '',
            'boxDriverId' => \Input::post('driverId') ? \Input::post('driverId') : '',
            'boxTemperature' => \Input::post('temperature') ? \Input::post('temperature') : '',
            'akku' => \Input::post('akku') ? \Input::post('akku') : '',
            'batterystatus' => $akku ? $akku : '',
            'battery'  => \Input::post('battery') ? \Input::post('battery') : '',
            'deviceId'  => \Input::post('device_id') ? \Input::post('device_id') : '',
            'boxStatus' => \Input::post('status') ? \Input::post('status') : '',
            'imei' => \Input::post('imei')
        );

        Tracking::setNewPosition("devices", \Input::post('latitude'), \Input::post('longitude'), \Input::post('accuracy'), \Input::post('speed'), \Input::post('date'), $arrAdditionalData);

        $this->arrReturn['error'] = false;
        $this->arrReturn['status'] = 905;

        return true;
    }

    /**
     *
     * @GET-Parameter vom SMS-Gateway
     * sender
     * timestamp (wann SMS-Gateway die SMS empfangen hat YYYYmmddHHiiss)
     * text -> Inhalt der SMS
     * msgid
     * apikey
     *
     * @return bool
     */
    private function trackingNewPositionFromSms()
    {

        $blnHasError = false;

        if ($this->blnDebugMode)
        {
            \Input::setPost('text',\Input::get('text'));
        }

        if (!\Input::post('text'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_data']);
            $blnHasError = true;
        }

        $strSmsContent = \Input::post('text');

        $arrSmsContent = explode(';', $strSmsContent);

        if (!is_array($arrSmsContent) || sizeof($arrSmsContent) == 0)
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['data_error']);
            $blnHasError = true;
        }

        if (!$blnHasError)
        {

            if ($arrSmsContent[0] == "newPosition")
            {

                $arrAdditionalData = array();

                $arrAdditionalData['trackUuid'] = $arrSmsContent[1];
                $strLatitude = $arrSmsContent[2];
                $strLongitude = $arrSmsContent[3];
                $strTimestamp = $arrSmsContent[4];

                $arrAdditionalData = array();
                if ($arrSmsContent[5])
                {
                    $strBatterystatus = $arrSmsContent[5];
                    $arrAdditionalData['batterystatus'] = $strBatterystatus;
                }

                $this->arrReturn['error'] = false;

                $this->arrReturn['track'] = Tracking::setNewPosition("tracks", $strLatitude, $strLongitude, 0, 0, $strTimestamp, $arrAdditionalData);


            }
        }


        return true;

    }

    private function trackingNewPosition()
    {

        if ($this->blnDebugMode)
        {
            \Input::setPost('track',\Input::get('track'));
            \Input::setPost('latitude',\Input::get('latitude'));
            \Input::setPost('longitude',\Input::get('longitude'));
        }

        $blnHasError = false;
        if (!\Input::post('track'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_track']);
            $blnHasError = true;
        }
        if (!\Input::post('latitude'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_latitude'] );
            $blnHasError = true;
        }
        if (!\Input::post('longitude'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_longitude']);
            $blnHasError = true;
        }
        if (!$blnHasError)
        {
            // optional data
            $longAccuracy = 0;
            $longSpeed = 0;
            $timeStamp = false;
            $arrAdditionalData = array();

            if (\Input::post('accuracy'))
            {
                $longAccuracy = \Input::post('accuracy');
            }
            if (\Input::post('speed'))
            {
                $longSpeed = \Input::post('speed');
            }
            if (\Input::post('timestamp'))
            {
                $timeStamp = \Input::post('timestamp');
            }

            if (\Input::post('positiontype'))
            {
                $arrAdditionalData['positiontype'] = \Input::post('positiontype');
            }
            if (\Input::post('imei'))
            {
                $arrAdditionalData['imei'] = \Input::post('imei');
            }
            if (\Input::post('batterystatus'))
            {
                $arrAdditionalData['batterystatus'] = \Input::post('batterystatus');
            }
            if (\Input::post('networkinfo'))
            {
                $arrAdditionalData['networkinfo'] = \Input::post('networkinfo');
            }
            if (\Input::post('track'))
            {
                $arrAdditionalData['trackUuid'] = \Input::post('track');
            }

            $this->arrReturn['error'] = false;
            $this->arrReturn['track'] = Tracking::setNewPosition("tracks", \Input::post('latitude'), \Input::post('longitude'), $longAccuracy, $longSpeed, $timeStamp, $arrAdditionalData);


        }

        return true;
    }

    private function trackingNewTrack()
    {

        if ($this->blnDebugMode)
        {
            \Input::setPost('user',\Input::get('user'));
            \Input::setPost('configuration',\Input::get('configuration'));
        }

        $blnHasError = false;
        if (!\Input::post('user'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_username']);
            $blnHasError = true;
        }
        if (!\Input::post('configuration'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_config']);
            $blnHasError = true;
        }
        $strName = "";
        if (\Input::post('name'))
        {
            $strName = \Input::post('name');
        }
        if (!$blnHasError)
        {
            $longAccuracy = 0;
            $longSpeed = 0;
            $timeStamp = false;
            $arrAdditionalData = array();
            $arrAdditionalData['additionalData'] = array();

            if (\Input::post('latitude'))
            {
                $arrAdditionalData['latitude'] = \Input::post('latitude');
            }

            if (\Input::post('longitude'))
            {
                $arrAdditionalData['longitude'] = \Input::post('longitude');
            }

            if (\Input::post('accuracy'))
            {
                $arrAdditionalData['accuracy'] = \Input::post('accuracy');
            }
            if (\Input::post('speed'))
            {
                $arrAdditionalData['speed'] = \Input::post('speed');
            }
            if (\Input::post('timestamp'))
            {
                $timeStamp = \Input::post('timestamp');
            }
            if (\Input::post('positiontype'))
            {
                $arrAdditionalData['positiontype'] = \Input::post('positiontype');
                $arrAdditionalData['additionalData']['positiontype'] = \Input::post('positiontype');
            }
            if (\Input::post('imei'))
            {
                $arrAdditionalData['imei'] = \Input::post('imei');
                $arrAdditionalData['additionalData']['imei'] = \Input::post('imei');
            }
            if (\Input::post('batterystatus'))
            {
                $arrAdditionalData['batterystatus'] = \Input::post('batterystatus');
                $arrAdditionalData['additionalData']['batterystatus'] = \Input::post('batterystatus');
            }
            if (\Input::post('networkinfo'))
            {
                $arrAdditionalData['networkinfo'] = \Input::post('networkinfo');
                $arrAdditionalData['additionalData']['networkinfo'] = \Input::post('networkinfo');
            }

            $this->arrReturn['error'] = false;

            $arrTrackData = Tracking::setNewTrack(\Input::post('configuration'), \Input::post('user'), \Input::post('privacy'), $strName, $timeStamp, $arrAdditionalData);

            $this->arrReturn['track'] = $arrTrackData;

            /* Store start location */
            /*if ($arrTrackData['trackId'] && \Input::post('latitude') && \Input::post('longitude'))
            {
              \Tracking::setNewPosition($arrTrackData['trackUuid'], \Input::post('latitude'), \Input::post('longitude'), $longAccuracy, $longSpeed, $timeStamp, $arrAdditionalData);
            }*/

        }

        return true;
    }

    private function trackingLoginUser()
    {
        if ($this->blnDebugMode)
        {
            \Input::setPost('user',\Input::get('user'));
            \Input::setPost('password',\Input::get('password'));
        }

        $blnHasError = false;

        if (!\Input::post('user') && !\Input::post('password'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_user_password']);
            $blnHasError = true;
        }
        elseif (!\Input::post('user'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_username']);
            $blnHasError = true;
        }
        elseif (!\Input::post('password'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_password']);
            $blnHasError = true;
        }

        if ($blnHasError)
        {
            return true;
        }

        \Input::setPost('username', \Input::post('user'));

        $this->import('FrontendUser', 'User');
        if (!$this->User->login())
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['wrong_login']);
            $blnHasError = true;
        }
        else
        {

            $arrTrackingConfig = Tracking::getTrackingConfig();

            if ($arrTrackingConfig['limitAccess'])
            {
                $arrAllowedGroups = $arrTrackingConfig['accessGroups'];

                $blnIsInAccessGroup = false;

                foreach ($arrAllowedGroups as $group)
                {
                    if ($this->User->isMemberOf($group))
                    {
                        $blnIsInAccessGroup = true;
                    }
                }

                if (!$blnIsInAccessGroup)
                {
                    $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_group_access']);
                    $blnHasError = true;
                }

            }

            if (!$blnHasError)
            {
                $this->import('Database');
                $strUniqId = md5(uniqid());
                $this->Database->prepare("UPDATE tl_member SET ssoHash=? WHERE id=?")->execute($strUniqId,$this->User->id);

                $this->arrReturn['error'] = false;
                $this->arrReturn['userId'] = $this->User->id;
                $this->arrReturn['userName'] = $this->User->username;
                $this->arrReturn['userHash'] = $strUniqId;
                $this->arrReturn['userRealName'] = ($this->User->firstname ? ($this->User->firstname . " ") : '') . $this->User->lastname;
                $this->arrReturn['trackingConfig'] = $arrTrackingConfig;
            }

        }

        return true;
    }

    private function trackingGetConfiguration()
    {
        $this->arrReturn['error'] = false;
        $this->arrReturn['trackingConfig'] = Tracking::getTrackingConfig();
        return true;
    }

    private function trackingTest()
    {
        $this->arrReturn = array
        (
            'error' => false
        );

        return true;
    }

    private function trackingFalseReturn()
    {
        return false;
    }

    private function trackingRegisterDevice()
    {

        $blnHasError = false;
        if (!\Input::post('configuration'))
        {
            $this->arrReturn = $this->getErrorReturn($GLOBALS['TL_LANG']['c4gTracking']['no_config']);
            $blnHasError = true;
        }

        if (!$blnHasError)
        {
            $strType = \Input::post('type');
            $strImei = \Input::post('imei');
            $strToken = \Input::post('token');
            $intCofingId = \Input::post('configuration');


            $arrSet = array
            (
                'pid' => $intCofingId,
                'tstamp' => time(),
                'type' => $strType,
                'imei' => $strImei,
                'token' => $strToken
            );

            $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findBy('imei', $strImei);

            if ($objDevice !== null)
            {

            }
            else
            {
                $objDevice = new \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel();
            }


            $objDevice->setRow($arrSet)->save();

            $this->arrReturn['error'] = false;
        }

        return true;
    }

    private function getErrorReturn($varMessage)
    {
        $arrReturn = array();
        $arrReturn['error'] = true;

        if (is_array($varMessage))
        {
            foreach ($varMessage as $key=>$varValue)
            {
                $arrReturn[$key] = $varValue;
            }
        }
        else
        {
            $arrReturn['message'] = $varMessage;
        }
        return $arrReturn;
    }

    private function trackingGetLastPositionForImei()
    {

        $intMaxAge = \Input::get('max') ? \Input::get('max') : 0;

        $objLastPosition = $this->getLastPositionForImei(\Input::get('imei'), $intMaxAge, true);

        if ($objLastPosition->id)
        {
            $this->arrReturn['error'] = false;
            $this->arrReturn['position'] = $objLastPosition->row();
        }

        return true;
    }

    public function getLastPositionForImei($varImei, $intMaxAge=0, $blnRequestForApi=false)
    {

        $objTrackingBox = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findByImeiEndpiece($varImei);

        if ($objTrackingBox === null)
        {
            if ($blnRequestForApi)
            {
                $this->arrReturn = $this->getErrorReturn(array
                (
                    "message" => "Device not found",
                    "status" => 900
                ));
                return true;
            }
            else
            {
                return false;
            }
        }

        $objLastPosition = $objTrackingBox->getRelated('lastPositionId');

        if ($objLastPosition === null) {
            if ($blnRequestForApi)
            {
                $this->arrReturn = $this->getErrorReturn(array
                (
                    "message" => "No position found"
                ));
                return true;
            }
            else
            {
                return false;
            }
        }

        if ($intMaxAge > 0)
        {
            $strTimeStamp = time() - (60 * $intMaxAge);

            if ($objLastPosition->tstamp < $strTimeStamp)
            {
                if ($blnRequestForApi)
                {
                    $this->arrReturn = $this->getErrorReturn(array
                    (
                        "message" => "No position found"
                    ));
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }

        return $objLastPosition;

    }

    public function getLastPositionForDevice($intDeviceId, $intMaxAge=0)
    {
        $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findOneBy('id', $intDeviceId);

        if ($objDevice === null)
        {
            return false;
        }

        $objLastPosition = $objDevice->getRelated('lastPositionId');

        if ($objLastPosition === null) {
            return false;
        }

        if ($intMaxAge > 0)
        {
            $strTimeStamp = time() - (60 * $intMaxAge);

            if ($objLastPosition->tstamp < $strTimeStamp)
            {
                return false;
            }
        }

        return $objLastPosition;
    }

    private function trackingGetLastPositionForMember()
    {
        $intMemberId = \Input::get('member');
        $intMaxAge = \Input::get('max') ? \Input::get('max') : 0;

        $this->arrReturn['error'] = false;
        $this->arrReturn['position'] = $this->getLastPositionForMember($intMemberId, $intMaxAge);

        return true;
    }

    public function getLastPositionForMember($intMemberId, $intMaxAge=0)
    {

        $db = \Database::getInstance();
        $objLastTrackInfo = $db->prepare("SELECT * FROM tl_c4g_tracking_positions WHERE id = (SELECT lastPositionId FROM tl_c4g_tracking_tracks WHERE member=? AND tl_c4g_tracking_tracks.lastPositionId > 0 ORDER BY lastPositionId DESC LIMIT 1)")
                                            ->execute($intMemberId);

        if ($objLastTrackInfo->numRows > 0)
        {
			if ($intMaxAge > 0)
			{
			    $strTimeStamp = time() - (60 * $intMaxAge);

			    if ($objLastTrackInfo->tstamp < $strTimeStamp)
			    {
			        return false;
			    }
			}
	        return $objLastTrackInfo;
        }
        return false;
    }

}