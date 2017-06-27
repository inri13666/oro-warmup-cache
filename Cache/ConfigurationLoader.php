<?php

namespace Okvpn\Bundle\WarmupCacheBundle\Cache;

use Oro\Component\Config\Loader\CumulativeConfigLoader;
use Oro\Component\Config\Loader\YamlCumulativeFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConfigurationLoader
{
    /** @var ParameterBag */
    protected $parameterBag;

    /** @var array */
    protected $cache;

    public function __construct()
    {
        $this->parameterBag = new ParameterBag();
    }

    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function setParameterBag(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param string $filePath
     * @param string $nodeName
     * @param string $resourceName
     * @param ContainerBuilder|null $containerBuilder
     *
     * @return array
     */
    public function loadConfiguration($filePath, $resourceName, $nodeName, ContainerBuilder $containerBuilder = null)
    {
        $key = $this->generateCacheKey($filePath, $resourceName);

        if (!isset($this->cache[$key])) {
            $configLoader = new CumulativeConfigLoader(
                $resourceName,
                new YamlCumulativeFileLoader($filePath)
            );
            $this->cache[$key] = $configLoader->load($containerBuilder);
        }

        $configs = [];
        $resources = $this->cache[$key];
        foreach ($resources as $resource) {
            if (array_key_exists($nodeName, (array)$resource->data) && is_array($resource->data[$nodeName])) {
                $configs[$resource->bundleClass] = $resource->data[$nodeName];
            }
        }

        return $this->parameterBag->resolveValue($configs);
    }

    /**
     * @param string $filePath
     * @param string $resourceName
     * @return string
     */
    protected function generateCacheKey($filePath, $resourceName)
    {
        return md5(sprintf('%s-%s', $filePath, $resourceName));
    }
}
