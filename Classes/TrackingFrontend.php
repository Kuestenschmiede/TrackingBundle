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

use con4gis\MapsBundle\Classes\Events\LoadLayersEvent;
use con4gis\MapsBundle\Resources\contao\models\C4gMapsModel;
use con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel;
use con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPositionsModel;
use Contao\System;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class TrackingFrontend
 * @package c4g
 */
class TrackingFrontend extends \Frontend
{
    private $arrAllowedLocationTypes = [
        'tPois',
        'tTracks',
        'tLive',
        'tBoxes',
    ];

    public function __construct()
    {
        parent::__construct();
//        if (FE_USER_LOGGED_IN) {
            $this->import('FrontendUser', 'User');
            $this->User->authenticate();
//        }
    }

    public function getInfoWindowContent($strTable, $intId, $arrData)
    {
        if ($strTable == 'devices') {
            $arrTrackingData = [];

            $blnUseTimeZoneSettings = false;

            if (strpos($intId, ';') !== false) {
                $arrTrackingInfoSettings = explode(';', $intId);

                if (is_array($arrTrackingInfoSettings)) {
                    foreach ($arrTrackingInfoSettings as $varTrackingInfo) {
                        if (strpos($varTrackingInfo, ',') !== false) {
                            $arrTrackingSingleInfo = explode(',', $varTrackingInfo);
                            if (is_array($arrTrackingSingleInfo)) {
                                if ($arrTrackingSingleInfo[0] == 'id') {
                                    $intPositionId = $arrTrackingSingleInfo[1];
                                }
                                if ($arrTrackingSingleInfo[0] == 'maps') {
                                    $intMapsItem = $arrTrackingSingleInfo[1];
                                }
                            }
                        }
                    }
                }

                $objLayer = C4gMapsModel::findById($intMapsItem);

                $strPopupContentRaw = '';

                $objPositions = C4gTrackingPositionsModel::findBy('id', $intPositionId);
                if ($objPositions !== null) {
                    $arrPositionData = $objPositions->row();

                    $arrTemplateData = [];

                    if (($objDevice = $objPositions->getRelated('device')) !== null) {
                        $arrDeviceData = $objDevice->row();

                        if ($arrDeviceData['timeZone'] && $arrDeviceData['timeZone'] != \Config::get('timeZone')) {
                            $blnUseTimeZoneSettings = true;
                            $strTimeZoneSettings = $arrDeviceData['timeZone'];
                        }

                        foreach ($arrDeviceData as $key => $varValue) {
                            $arrTemplateData['device' . ucfirst($key)] = $varValue;
                        }
                    }

                    foreach ($arrPositionData as $key => $varValue) {
                        $arrTemplateData['position' . ucfirst($key)] = $varValue;
                    }
                }

                if ($objLayer !== null) {
                    foreach ($arrTemplateData as $key => $varValue) {
                        if (strpos(strtolower($key), 'tstamp') !== false) {
                            if ($key == 'positionTstamp') {
                                if ($blnUseTimeZoneSettings) {

                                  // store local and device timezone
                                    $dateTimeZoneDevice = new \DateTimeZone($strTimeZoneSettings);
                                    $dateTimeZoneServer = new \DateTimeZone(\Config::get('timeZone'));

                                    // get one date-time-object for device timezone
                                    $dateTimeDevice = new \DateTime('now', $dateTimeZoneDevice);

                                    // get the offset of the timezone
                                    $timeOffset = $dateTimeZoneServer->getOffset($dateTimeDevice);

                                    // recalculate timestamp with given offset
                                    $varTimeStamp = $varValue + $timeOffset;

                                    $arrTemplateData[$key] = \Date::parse(\Config::get('datimFormat'), $varTimeStamp);
                                } else {
                                    $arrTemplateData[$key] = \Date::parse(\Config::get('datimFormat'), $varValue);
                                }
                            } else {
                                $arrTemplateData[$key] = \Date::parse(\Config::get('datimFormat'), $varValue);
                            }
                        }

                        if (is_array(deserialize($varValue))) {
                            unset($arrTemplateData[$key]);
                            $arrArrayData = deserialize($varValue, true);

                            foreach ($arrArrayData as $dataKey => $dataVarValue) {
                                $arrTemplateData[$key . ucfirst($dataKey)] = $dataVarValue;
                            }
                        }
                    }

                    if ($objLayer->popupType == 'template') {
                        $objPopupTemplate = new \FrontendTemplate($objLayer->popupTemplate);
                        $objPopupTemplate->setData($arrTemplateData);
                        $objLayer->popup_info = $objPopupTemplate->parse();
                        $objLayer->popup_info = $this->replaceInsertTags($objLayer->popup_info);
                    }

                    if ($objLayer->popup_info) {
                        $strPopupContentRaw = $objLayer->popup_info;

                        //print_r($arrTemplateData);

                        $regSearch = '/(\$\{\w*\})/';

                        if (preg_match_all($regSearch, $strPopupContentRaw, $arrPlaceholder, PREG_PATTERN_ORDER)) {
                            foreach ($arrPlaceholder[0] as $strPlaceholder) {
                                if ($strPlaceholder == '${allData}') {
                                    $strAllData = '<dl>';

                                    foreach ($arrTemplateData as $key => $varValue) {
                                        $strAllData .= '<dt>' . $key . '<dt>';
                                        $strAllData .= '<dd>' . $varValue . '<dd>';
                                    }
                                    $strAllData .= '</dl>';

                                    $strPopupContentRaw = str_replace($strPlaceholder, $strAllData, $strPopupContentRaw);
                                } else {
                                    $strPlaceholderRaw = str_replace(['${', '}'], '', $strPlaceholder);
                                    $strPopupContentRaw = str_replace($strPlaceholder, $arrTemplateData[$strPlaceholderRaw], $strPopupContentRaw);
                                }
                            }
                        }
                    }
                }

                $arrData['content'] = $strPopupContentRaw;
            }
        }

        return $arrData;
    }

    public function addLocations(
        LoadLayersEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $child = $event->getLayerData();
        $level = $child['pid'];
        $stringClass = $GLOBALS['con4gis']['stringClass'];
        $objMap = C4gMapsModel::findById($child['id']);
        $child['raw'] = $objMap;
        if (in_array($child['type'], $this->arrAllowedLocationTypes)) {
            $arrData = [];
            $arrData['excludeFromSingleLayer'] = true;
            $arrData['async_content'] = 5;

            switch ($child['type']) {
                case 'tPois':
                    $arrData['pid'] = $level;
                    $arrData['id'] = $child['id'];
                    $arrData['type'] = 'none';
                    $arrData['display'] = $child['display'];
                    $arrData['name'] = $stringClass::decodeEntities($child['name']);
                    $arrData['hide'] = $child['hide'] > 0 ? $child['hide'] : '';
                    $arrChildData = $this->getPoiData($child);
                    if (sizeof($arrChildData) == 0 && $child->tDontShowIfEmpty) {
                        return;
                    }

                        $arrData['hasChilds'] = true;
                        $arrData['childsCount'] = sizeof($arrChildData);
                        $arrData['childs'] = $arrChildData;

                    break;
                case 'tTracks':
                    $arrData['pid'] = $level;
                    $arrData['id'] = $child['id'];
                    $arrData['type'] = 'none';
                    $arrData['display'] = $child['display'];
                    $arrData['name'] = $stringClass::decodeEntities($child['name']);
                    $arrData['hide'] = $child['hide'];
                    $arrChildData = $this->getTrackData($child);
                    if (sizeof($arrChildData) == 0 && $child->tDontShowIfEmpty) {
                        return;
                    }

                        $arrData['hasChilds'] = true;
                        $arrData['childsCount'] = sizeof($arrChildData);
                        $arrData['childs'] = $arrChildData;

                    break;
                case 'tBoxes':
                    $arrData['pid'] = $level;
                    $arrData['id'] = $child['id'];
                    $arrData['type'] = 'none';
                    $arrData['display'] = $child['display'];
                    $arrData['name'] = $stringClass::decodeEntities($child['name']);
                    $arrData['hide'] = $child['hide'];
                    $arrData['content'] = '';//$child['hide'];
                    $arrChildData = $this->getBoxTrackData($child);
                    if (sizeof($arrChildData) == 0 && $child->tDontShowIfEmpty) {
                        return;
                    }

                        $arrData['hasChilds'] = true;
                        $arrData['childsCount'] = sizeof($arrChildData);
                        $arrData['childs'] = $arrChildData;

                    break;
                case 'tLive':
                    $arrData['pid'] = $level;
                    $arrData['id'] = $child['id'];
                    $arrData['origType'] = 'liveTracking';
                    $arrData['locstyle'] = $child['raw']->locstyle;
                    $arrData['display'] = $child['display'];
                    $arrData['name'] = $stringClass::decodeEntities($child['name']);
                    $arrData['hide'] = $child['hide'];
                    $arrData['filterable'] = $child['raw']->isFilterable ? 1 : 0;

                    if ($child['raw']->liveTrackingType == 'tLive_alleach' || $child['raw']->liveTrackingType == 'tLive_groupeach' || $child['raw']->liveTrackingType == 'tLive_deviceeach') {
                        $arrData['split_geojson'] = 1;
                        $arrData['geojson_attributes'] = "name";
                        if ($child['raw']->isFilterable) {
                            $arrData['filterable'] = 0;//"&maps=" . $child['id'];
                        }
                    }
                    if ($child['raw']->liveTrackingType == 'tLive_group' || $child['raw']->liveTrackingType == 'tLive_groupeach') {
                        $strUrl = $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getLive&maps=' . $child['id'] . '&useGroup=' . $child['id'];
                        if ($child['raw']->isFilterable) {
                            $arrData['filterable'] = [];
                            $arrData['filterable']['urlParam'] = '&maps=' . $child['id'] . '&useGroup=' . $child['id'];

                            if ($child['raw']->filterLocationStyle) {
                                $arrData['filterable']['locationStyle'] = $child['raw']->filterLocationStyle;
                            }
                        }
                    } elseif ($child['raw']->liveTrackingType == 'tLive_device' || $child['raw']->liveTrackingType == 'tLive_deviceeach') {
                        $arrDevices = deserialize($child['raw']->liveTrackingDevices, true);
                        $strUrl = $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getLive&maps=' . $child['id'] . '&id[]=' . implode('&id[]=', $arrDevices);
                        if ($child['raw']->isFilterable) {
                            $arrData['filterable'] = [];
                            $arrData['filterable']['urlParam'] = '&maps=' . $child['id'];

                            if ($child['raw']->filterLocationStyle) {
                                $arrData['filterable']['locationStyle'] = $child['raw']->filterLocationStyle;
                            }
                            //$arrData['filterable'] = array(
                          //    "type" => "tLive_device",
                          //    "maps" => $child['id'],
                          //    "devices" => $arrDevices
                          //);//"maps=" . $child['id'] . "&id[]=" . implode('&id[]=', $arrDevices);
                        }
                    } else {
                        $strUrl = $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getLive&maps=' . $child['id'] . '';
                        if ($child['raw']->isFilterable) {
                            $arrData['filterable'] = [];
                            $arrData['filterable']['urlParam'] = '&maps=' . $child['id'];

                            if ($child['raw']->filterLocationStyle) {
                                $arrData['filterable']['locationStyle'] = $child['raw']->filterLocationStyle;
                            }
                        }
                    }

                    $arrData['content'] = [
                    [
                      'type' => 'urlData',
                      'format' => 'GeoJSON',
                      'locationStyle' => $child['locstyle'] ?: $child['raw']->locstyle,
                      'data' => [
                        'url' => $strUrl,
                      ],
                      'settings' => [
                        'loadAsync' => true,
                        'refresh' => true,
                        'interval' => 60000,
                        'crossOrigin' => false,
                      ],
                    ],
                  ];


                //$GLOBALS['TL_BODY'][] = '<script src="system/modules/con4gis_tracking/assets/liveTracking.js"></script>';

                break;
            }
            $event->setLayerData($arrData);

            return $arrData;
        }
    }

    protected function getLiveChildData($child)
    {
        $stringClass = $GLOBALS['con4gis']['stringClass'];
        //var_dump($child);
        $arrTrackData = [];

        if ($child['raw']->liveTrackingType == 'tLive_group' || $child['raw']->liveTrackingType == 'tLive_groupeach') {
            // 'tLive_all', 'tLive_alleach', 'tLive_group', 'tLive_group', 'tLive_device'
            $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findBy('mapStructureId', $child['id'], ['order' => 'name']);
        } elseif ($child['raw']->liveTrackingType == 'tLive_device' || $child['raw']->liveTrackingType == 'tLive_deviceeach') {
            //print_r($child['raw']->liveTrackingDevices);
            $arrDevices = deserialize($child['raw']->liveTrackingDevices, true);
            $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findMultipleByIds($arrDevices);
        } else {
            $objDevice = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingDevicesModel::findAll(['order' => 'name']);
        }

        if ($objDevice !== null) {
            while ($objDevice->next()) {
                $arrFilterData = [];

                if ($child['raw']->isFilterable) {
                    $arrFilterData['urlParam'] = '&maps=' . $child['id'] . '&id=' . $objDevice->id;

                    if ($child['raw']->filterLocationStyle) {
                        $arrFilterData['locationStyle'] = $child['raw']->filterLocationStyle;
                    }
                }

                $arrTrackData[] = [
            'pid' => $child['id'],
            'id' => $child['id'] . $objDevice->id,
            'name' => $objDevice->name ? $stringClass::decodeEntities($objDevice->name) : $objDevice->id,
            'hide' => $child['hide'] > 0 ? $child['hide'] : '',
            'filterable' => $child['raw']->isFilterable ? $arrFilterData : 0,
            'display' => $child['display'],
            'excludeFromSingleLayer' => true,
            'async_content' => true,
            'content' => [
              [
                'id' => '',
                'type' => 'urlData',
                'format' => 'GeoJSON',
                'locationStyle' => $this->checkAndReparseLocationStyle($objDevice->locationStyle ? $objDevice->locationStyle : $child['raw']->locstyle, $child, $objDevice),
                'data' => [
                  'url' => $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService/getLive/?maps=' . $child['id'] . '&id=' . $objDevice->id,
                ],
                'settings' => [
                  'loadAsync' => true,
                  'refresh' => true,
                  'crossOrigine' => false,
                    'interval' => 60000,
                ],
              ],
            ],
          ];
            }
        }

        return $arrTrackData;
    }

    protected function getBoxTrackData($child)
    {
        $arrTrackData = [];

        $objBoxes = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingBoxesModel::findAll();

        if ($objBoxes !== null) {
            while ($objBoxes->next()) {
                $arrTrackData[] = [
                    'pid' => $child['id'],
                    'id' => $child['id'] . $objBoxes->id,
                    'type' => 'ajax',
                    'url' => $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getBoxTrack&id=' . $objBoxes->id,
                    'name' => $child['name'] ? ($objBoxes->name . ' (' . \Date::parse('d.m.Y H:i', $objBoxes->tstamp) . ')') : '',
                    'hide' => $child['hide'] > 0 ? $child['hide'] : '',
                    'display' => $child['display'],
                    'popupInfo' => $objBoxes->name,
                    'content' => [
                        [
                            'id' => '',
                            'type' => 'urlData',
                            'format' => 'GeoJSON',
                            'locationStyle' => $child['raw']->locstyle,
                            'data' => [
                                'url' => $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getBoxTrack&id=' . $objBoxes->id,
                                'popup' => [
                                    'content' => '',
                                ],
                            ],
                            'settings' => [
                                'loadAsync' => true,
                                'refresh' => false,
                                'crossOrigine' => false,
                                'boundingBox' => false,
                            ],
                        ],
                    ],
                ];
            }
        }

        return $arrTrackData;
    }

    protected function getTrackData($child)
    {
        $arrTrackData = [];

        $strType = $child['raw']->memberVisibility ? $child['raw']->memberVisibility : 'all';

        $arrMember = [];
        $arrVisibility = [];

        $blnUseDatabaseStatus = false;
        if ($child['raw']->useDatabaseStatus) {
            $arrAllowedStatus = deserialize($child['raw']->databaseStatus);
            if (is_array($arrAllowedStatus) && sizeof($arrAllowedStatus) > 0) {
                $blnUseDatabaseStatus = true;
            }
        }

        switch ($strType) {
            case 'own':
              if (FE_USER_LOGGED_IN) {
                  $this->import('FrontendUser', 'User');
                  $arrMember[] = $this->User->id;
                  if ($blnUseDatabaseStatus) {
                      $arrVisibility = $arrAllowedStatus;
                  }
                  $objTracks = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel::findWithPositions($arrMember, $arrVisibility);
              }

              break;
            case 'ownGroups':
              if (FE_USER_LOGGED_IN) {
                  $this->import('FrontendUser', 'User');
                  $arrMemberGroups = $this->User->__get('groups');
                  if (is_array($arrMemberGroups)) {
                      foreach ($arrMemberGroups as $memberGroup) {
                          $objMember = $this->Database->prepare('SELECT id,username FROM tl_member WHERE groups LIKE ?')
                                                ->execute('%"' . $memberGroup . '"%');
                          if ($objMember->numRows > 0) {
                              while ($objMember->next()) {
                                  if (!in_array($objMember->id, $arrMember)) {
                                      $arrMember[] = $objMember->id;
                                  }
                              }
                          }
                      }
                  }
                  $arrVisibility[] = 'owngroups';
                  $arrVisibility[] = 'public';
                  if ($blnUseDatabaseStatus) {
                      $arrVisibility = $arrAllowedStatus;
                  }
                  $objTracks = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel::findWithPositions($arrMember, $arrVisibility);
              }

              break;
            case 'specialGroups':
              $arrMemberGroups = deserialize($child->specialGroups, true);
              if (is_array($arrMemberGroups)) {
                  foreach ($arrMemberGroups as $memberGroup) {
                      $objMember = $this->Database->prepare('SELECT id,username FROM tl_member WHERE groups LIKE ?')
                                              ->execute('%"' . $memberGroup . '"%');
                      if ($objMember->numRows > 0) {
                          while ($objMember->next()) {
                              if (!in_array($objMember->id, $arrMember)) {
                                  $arrMember[] = $objMember->id;
                              }
                          }
                      }
                  }
              }
              $arrVisibility[] = 'membergroups';
              $arrVisibility[] = 'public';
              if ($blnUseDatabaseStatus) {
                  $arrVisibility = $arrAllowedStatus;
              }
              $objTracks = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel::findWithPositions($arrMember, $arrVisibility);

              break;
            case 'specialMember':
              $arrVisibility[] = 'public';
              if ($blnUseDatabaseStatus) {
                  $arrVisibility = $arrAllowedStatus;
              }
              if ($child->specialMembers) {
                  $arrMember = deserialize($child->specialMembers, true);
              }
              $objTracks = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel::findWithPositions($arrMember, $arrVisibility);

              break;
            case 'all':

                $arrVisibility[] = 'public';
                if ($blnUseDatabaseStatus) {
                    $arrVisibility = $arrAllowedStatus;
                }
                $objTracks = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingTracksModel::findWithPositions($arrMember, $arrVisibility);

                break;
            default:

                break;
        }

        if ($objTracks !== null) {
            while ($objTracks->next()) {
                $arrTrackData[] = [
                    'pid' => $child['id'],
                    'id' => $child['id'] . $objTracks->id,
                    'type' => 'ajax',
                    'url' => $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getTrack&id=' . $objTracks->uuid,
                    'name' => $child['name'] ? ($objTracks->name . ' (' . \Date::parse('d.m.Y H:i', $objTracks->tstamp) . ')') : '',
                    'hide' => $child['hide'] > 0 ? $child['hide'] : '',
                    'display' => $child['display'],
                    'popupInfo' => $objTracks->name,
                    'content' => [
                        [
                            'id' => '',
                            'type' => 'urlData',
                            'format' => 'GeoJSON',
                            'locationStyle' => $child['raw']->locstyle,
                            'data' => [
                                'url' => $GLOBALS['con4gis']['tracking']['apiBaseUrl'] . '/trackingService?method=getTrack&id=' . $objTracks->uuid,
                                'popup' => [
                                    'content' => '',
                                ],
                            ],
                            'settings' => [
                                'loadAsync' => true,
                                'refresh' => false,
                                'crossOrigine' => false,
                                'boundingBox' => false,
                            ],
                        ],
                    ],
                ];
            }
        }

        return $arrTrackData;
    }

    protected function getPoiData($child)
    {
        $arrPoiData = [];
        $stringClass = $GLOBALS['con4gis']['stringClass'];

        $strType = $child['raw']->memberVisibility ? $child['raw']->memberVisibility : 'all';

        $arrMember = [];
        $arrVisibility = [];

        $blnUseDatabaseStatus = false;
        if ($child['raw']->useDatabaseStatus) {
            $arrAllowedStatus = deserialize($child['raw']->databaseStatus);
            if (is_array($arrAllowedStatus) && sizeof($arrAllowedStatus) > 0) {
                $blnUseDatabaseStatus = true;
            }
        }

        switch ($strType) {
            case 'own':
              if (FE_USER_LOGGED_IN) {
                  $this->import('FrontendUser', 'User');
                  $arrMember[] = $this->User->id;
                  if ($blnUseDatabaseStatus) {
                      $arrVisibility = $arrAllowedStatus;
                  }
                  $objPois = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel::findWithMagic($arrMember, $arrVisibility);
              }

              break;
            case 'ownGroups':
              if (FE_USER_LOGGED_IN) {
                  $this->import('FrontendUser', 'User');
                  $arrMemberGroups = $this->User->__get('groups');
                  if (is_array($arrMemberGroups)) {
                      foreach ($arrMemberGroups as $memberGroup) {
                          $objMember = $this->Database->prepare('SELECT id,username FROM tl_member WHERE groups LIKE ?')
                                                ->execute('%"' . $memberGroup . '"%');
                          if ($objMember->numRows > 0) {
                              while ($objMember->next()) {
                                  if (!in_array($objMember->id, $arrMember)) {
                                      $arrMember[] = $objMember->id;
                                  }
                              }
                          }
                      }
                  }
                  $arrVisibility[] = 'owngroups';
                  $arrVisibility[] = 'public';
                  if ($blnUseDatabaseStatus) {
                      $arrVisibility = $arrAllowedStatus;
                  }
                  $objPois = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel::findWithMagic($arrMember, $arrVisibility);
              }

              break;
            case 'specialGroups':
              $arrMemberGroups = deserialize($child->specialGroups, true);
              if (is_array($arrMemberGroups)) {
                  foreach ($arrMemberGroups as $memberGroup) {
                      $objMember = $this->Database->prepare('SELECT id,username FROM tl_member WHERE groups LIKE ?')
                                              ->execute('%"' . $memberGroup . '"%');
                      if ($objMember->numRows > 0) {
                          while ($objMember->next()) {
                              if (!in_array($objMember->id, $arrMember)) {
                                  $arrMember[] = $objMember->id;
                              }
                          }
                      }
                  }
              }
              $arrVisibility[] = 'membergroups';
              $arrVisibility[] = 'public';
              if ($blnUseDatabaseStatus) {
                  $arrVisibility = $arrAllowedStatus;
              }
              $objPois = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel::findWithMagic($arrMember, $arrVisibility);

              break;
            case 'specialMember':
              $arrVisibility[] = 'public';
              if ($blnUseDatabaseStatus) {
                  $arrVisibility = $arrAllowedStatus;
              }
              if ($child->specialMembers) {
                  $arrMember = deserialize($child->specialMembers, true);
              }
              $objPois = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel::findWithMagic($arrMember, $arrVisibility);

              break;
            case 'all':
                $arrVisibility[] = 'public';
                if ($blnUseDatabaseStatus) {
                    $arrVisibility = $arrAllowedStatus;
                }
                $objPois = \con4gis\TrackingBundle\Resources\contao\models\C4gTrackingPoisModel::findWithMagic($arrMember, $arrVisibility);

                break;
            default:

                break;
        }

        if ($objPois !== null) {
            while ($objPois->next()) {
                $objPosition = $objPois->getRelated('positionId');

                if ($objPosition === null) {
                    continue;
                }

                if (!$objPosition->longitude || !$objPosition->longitude) {
                    continue;
                }
                $arrPoiData[] = [
                    'pid' => $child['id'],
                    'id' => $child['id'] . $objPois->id,
                    'type' => 'single',
                    'display' => true,
                    'name' => $child['name'] ? ($objPois->name . ' (' . \Date::parse('d.m.Y H:i', $objPois->tstamp) . ')') : '',
                    'hide' => $child['hide'],
                    'content' => [
                        [
                            'id' => '3',
                            'type' => 'GeoJSON',
                            'format' => 'GeoJSON',
                            'origType' => 'single',
                            'locationStyle' => '4',
                            'data' => [
                                'type' => 'Feature',
                                'geometry' => [
                                    'type' => 'Point',
                                    'coordinates' => [
                                        (float) $objPosition->longitude,
                                        (float) $objPosition->latitude,
                                    ],
                                ],
                                'properties' => [
                                    'projection' => 'EPSG:4326',
                                ],
                            ],
                        ],
                    ],
                ];

                $blnUsePopUp = false;
                $strPopUpInfo = '';
                if ($child->popup_info && $child->popup_info != '') {
                    $blnUsePopUp = true;
                    $arrDataForPopup = [
                    'name' => $objPois->name,
                    'time' => \Date::parse('d.m.Y H:i', $objPois->tstamp),
                    'longitude' => $objPois->longitude,
                    'latitude' => $objPois->latitude,
                  ];
                    $strPopUpInfo = $stringClass::parseSimpleTokens($child->popup_info, $arrDataForPopup);
                }

                /*$arrPoiData[] = array
                (
                    'parent' => $child->id . $objPois->id,
                    'geox' => $objPois->longitude,
                    'geoy' => $objPois->latitude,
                    'locstyle' => $child->locstyle,
                    'label' => '',
                    'onclick_zoomto' => '0',
                    'minzoom' => '0',
                    'maxzoom' => '0',
                    'graphicTitle' => '',
                    'popupInfo' => $strPopUpInfo,
                    'linkurl' => ''
                );*/
            }
        }

        return $arrPoiData;
    }

    public function runCronJob()
    {
        $objPoisForDelete = $this->Database->prepare('SELECT * FROM tl_c4g_tracking_pois WHERE forDelete=?')
                                         ->execute('1');
        if ($objPoisForDelete->numRows > 0) {
            $this->Database->prepare('DELETE FROM tl_c4g_tracking_pois WHERE forDelete=?')
                       ->execute('1');
        }

        $objTracksForDelete = $this->Database->prepare('SELECT * FROM tl_c4g_tracking_tracks WHERE forDelete=?')
                                         ->execute('1');

        if ($objTracksForDelete->numRows > 0) {
            while ($objTracksForDelete->next()) {
                $intTrackUuid = $objTracksForDelete->uuid;
                $this->Database->prepare('DELETE FROM tl_c4g_tracking_positions WHERE track_uuid=?')
                          ->execute($intTrackUuid);
                $this->Database->prepare('DELETE FROM tl_c4g_tracking_tracks WHERE id=?')
                          ->execute($objTracksForDelete->id);
            }
        }
    }

    private function checkAndReparseLocationStyle($intCurrentLocationStyle, $arrMapsSettings, $objDevice)
    {
        if ($arrMapsSettings['raw']->useIgnitionStatusStyle) {
            // use ignition status for location style

            if (($blnIgnitionStatusIsOn = Tracking::getIgnitionStatus($objDevice->id)) !== null) {
                if ($blnIgnitionStatusIsOn) {
                    return $arrMapsSettings['raw']->ignitionStatusStyleOn;
                }

                return $arrMapsSettings['raw']->ignitionStatusStyleOff;
            }
        }

        return $intCurrentLocationStyle;
    }

    private function getIgnitionStatus($deviceId)
    {
        $device = C4gTrackingDevicesModel::findByPk($deviceId);
        $lastPositionId = $device->lastPositionId;
        $position = C4gTrackingPositionsModel::findByPk($lastPositionId);
        if ($position) {
            // don't search the whole table if last position was a ignition signal
            if ($position->boxStatus == 12 || $position->boxStatus == 13) {
                return $position->boxStatus == 12;
            }
            // fallback
            return Tracking::getIgnitionStatus($deviceId);
        }

        return false;
    }
}
