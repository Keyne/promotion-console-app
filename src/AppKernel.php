<?php
/**
 * Created by PhpStorm.
 * User: Keyne
 * Date: 13/10/2018
 * Time: 22:31
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [];
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return getenv("CACHE_DIR") ? getenv("CACHE_DIR") : $this->getRootDir()."/../var/cache";
    }

    public function getLogDir()
    {
        return getenv("LOG_DIR") ? getenv("LOG_DIR") : $this->getRootDir()."/../var/logs";
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/../config/config_'.$this->getEnvironment().'.yml');
    }
}
