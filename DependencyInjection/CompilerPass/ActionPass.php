<?php

namespace Okvpn\Bundle\WarmupCacheBundle\DependencyInjection\CompilerPass;

use Okvpn\Bundle\WarmupCacheBundle\Action\Configuration\ConfigurationProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ActionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processDefinition($container, 'oro_action.configuration.provider.operations');
        $this->processDefinition($container, 'oro_action.configuration.provider.action_groups');
    }

    /**
     * @param ContainerBuilder $container
     * @param string $id
     */
    protected function processDefinition(ContainerBuilder $container, $id)
    {
        $reference = new Reference('okvpn_warmup_cache.configuration_loader');

        if ($container->hasDefinition($id)) {
            $definition = $container->getDefinition($id);
            $definition->setClass(ConfigurationProvider::class);
            $definition->addMethodCall('setConfigurationLoader', [$reference]);
            $definition->addTag('okvpn.cache_warmer', ['dumper' => 'okvpn_warmup_cache.dumper']);
        }
    }
}
