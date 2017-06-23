<?php

namespace Okvpn\Bundle\WarmupCacheBundle\Action\Configuration;

use Doctrine\Common\Collections\Collection;
use Okvpn\Bundle\WarmupCacheBundle\Cache\ConfigCacheWarmerInterface;
use Okvpn\Bundle\WarmupCacheBundle\Cache\ConfigurationLoader;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Oro\Bundle\ActionBundle\Configuration\ConfigurationProvider as BaseConfigurationProvider;
use Oro\Component\Config\Merger\ConfigurationMerger;

class ConfigurationProvider extends BaseConfigurationProvider implements ConfigCacheWarmerInterface
{
    const CONFIG_FILE_PATH = 'Resources/config/oro/actions.yml';

    /** @var ConfigurationLoader */
    protected $configurationLoader;

    /**
     * @param ConfigurationLoader $configurationLoader
     */
    public function setConfigurationLoader(ConfigurationLoader $configurationLoader)
    {
        $this->configurationLoader = $configurationLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUpResourceCache(ContainerBuilder $containerBuilder)
    {
        $this->clearCache();
        $this->cache->save($this->rootNode, $this->resolveConfiguration(null, $containerBuilder));
    }

    /**
     * @param bool $ignoreCache
     * @param Collection $errors
     * @return array
     * @throws InvalidConfigurationException
     */
    public function getConfiguration($ignoreCache = false, Collection $errors = null)
    {
        return parent::getConfiguration($ignoreCache, $errors);
    }

    /**
     * @param Collection $errors
     * @param ContainerBuilder $containerBuilder
     * @return array
     * @throws InvalidConfigurationException
     */
    protected function resolveConfiguration(Collection $errors = null, ContainerBuilder $containerBuilder = null)
    {
        $rawConfiguration = array_merge(
            $this->rawConfiguration,
            $this->configurationLoader->loadConfiguration(
                self::CONFIG_FILE_PATH,
                'oro_action',
                $this->rootNode,
                $containerBuilder
            )
        );

        $merger = new ConfigurationMerger($this->kernelBundles);
        $configs = $merger->mergeConfiguration($rawConfiguration);
        $data = [];

        try {
            if (!empty($configs)) {
                $data = $this->configurationDefinition->processConfiguration($configs);

                $this->validator->validate($data, $errors);
            }
        } catch (InvalidConfigurationException $e) {
            throw new InvalidConfigurationException(sprintf('Can\'t parse configuration. %s', $e->getMessage()));
        }

        return $data;
    }
}
