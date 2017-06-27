<?php

namespace Okvpn\Bundle\WarmupCacheBundle\Cache;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface ConfigCacheWarmerInterface
{
    /**
     * @param ContainerBuilder $containerBuilder
     */
    public function warmUpResourceCache(ContainerBuilder $containerBuilder);
}
