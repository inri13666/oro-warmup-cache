<?php

namespace Okvpn\Bundle\WarmupCacheBundle;

use Okvpn\Bundle\WarmupCacheBundle\DependencyInjection\CompilerPass\ActionPass;
use Okvpn\Bundle\WarmupCacheBundle\DependencyInjection\CompilerPass\CacheWarmerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OkvpnWarmupCacheBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ActionPass());
        $container->addCompilerPass(new CacheWarmerPass());
        $this->removeOldActionCompilerPass($container->getCompilerPassConfig());
    }


    /**
     * @param PassConfig $passConfig
     */
    protected function removeOldActionCompilerPass(PassConfig $passConfig)
    {
        $passes = $passConfig->getAfterRemovingPasses();
        foreach ($passes as $key => $pass) {
            if (get_class($pass) === 'Oro\Bundle\ActionBundle\DependencyInjection\CompilerPass\ConfigurationPass') {
                unset($passes[$key]);
            }
        }

        $passConfig->setAfterRemovingPasses($passes);
    }
}
