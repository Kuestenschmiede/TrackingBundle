<?php
/**
 * Created by PhpStorm.
 * User: claudioross
 * Date: 08.06.18
 * Time: 12:30
 */

namespace con4gis\TrackingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class con4gisTrackingExtension extends Extension
{

    private $files = [
        "services.yml"
    ];
    /**
     * {@inheritdoc}
     */
    public function load(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        foreach ($this->files as $file) {
            $loader->load($file);
        }
    }
}


