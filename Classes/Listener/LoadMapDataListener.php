<?php

namespace con4gis\TrackingBundle\Classes\Listener;

use con4gis\MapsBundle\Classes\Events\LoadMapdataEvent;
use con4gis\MapsBundle\Resources\contao\models\C4gMapProfilesModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LoadMapDataListener
{
    public function onLoadMapDataDoIt(
        LoadMapdataEvent $event,
        $eventName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $mapData = $event->getMapData();
        $profile = C4gMapProfilesModel::findByPk($mapData['profile']);
        if ($profile && $profile->ownGeosearch) {
            unset($mapData['geosearch']['url']);
        }
        $event->setMapData($mapData);
    }
}
