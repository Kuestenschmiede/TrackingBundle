
if (typeof globals === "undefined")
{
  var globals = {};
}


function liveTracking(map, importLayer, data, setStyleHelper) {
    var timeout;
    var importLayer = importLayer;
    
    globals.liveTrackingData = data;
    
    var layerKey = importLayer.key;
    
    importLayer.redraw();
    
    map.events.register('changelayer', null, function(evt){
       if(evt.property === "visibility") {
         if (evt.layer.key == layerKey) {
           if (evt.layer.visibility) {
             importLayer.destroyFeatures();
             liveRequest();
           } else {
             window.clearTimeout(timeout)
           }
         }
       }
    });

    var fnLiveCallback = function urlRequestHandler(request) {
        var requestData = JSON.parse(request.responseText);
        importLayer.destroyFeatures();
        if (!requestData.error)
        {
          var options = {
              internalProjection : map.getProjectionObject(),
              externalProjection : new OpenLayers.Projection('EPSG:4326')
          };
          var importFormat = new OpenLayers.Format.GeoJSON(options);
          
          var aFeature = importFormat.read(requestData);
          if (typeof globals.liveTrackingData.locstyle !== "undefined")
          {
             for (var i=0; i<aFeature.length; i++)
            {
              aFeature[i]['style'] = globals.locationStyles[globals.liveTrackingData.locstyle];
            }
          }
          importLayer.addFeatures(aFeature);
        }
        timeout = window.setTimeout(function(){liveRequest()}, 10000);
    }

    var liveRequest = function() {
        OpenLayers.Request.GET({
            url : apiBaseUrl + '/trackingService?method=getLive',
            callback: fnLiveCallback
        });
    };

    if (importLayer.visibility) {
      liveRequest();
    }

}