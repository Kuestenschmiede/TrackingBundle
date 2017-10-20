con4gis-Tracking
================
The tracking brick of the Contao GIS-kit **con4gis**. A telematic interface. Usefull with con4gis-Maps and con4gis-TrackingAndroid.

**Git Repository:** TrackingBundle  
**Composer Vendor/Package:** [con4gis/tracking](https://packagist.org/packages/con4gis/tracking)  
**Website:** [con4gis.org](https://con4gis.org)

**Requires:**
- [Contao](https://github.com/contao/core) (***4.4.x***)   
For Contao 3 you can use [con4igs_tracking](https://github.com/Kuestenschmiede/con4gis_tracking/releases) Extension.
- [CoreBundle](https://github.com/Kuestenschmiede/CoreBundle/releases) (*latest stable release*)
- [MapsBundle](https://github.com/Kuestenschmiede/MapsBundle/releases) (*latest stable release*)

**Extendable:**
- [TrackingAndroidBundle](https://github.com/Kuestenschmiede/TrackingAndroidBundle/releases) (*latest stable release*)
  
**And with all other con4gis and Contao bundles**  
con4gis is not a connectable application. It's a collection of content 
elements for your website. So you can build your own individual and limitless web applications.

## Routing configuration
Must be extended in _app/config/routing.yml_

```
con4gis_tracking:
    resource: "@con4gisTrackingBundle/Resources/config/routing.yml"
```