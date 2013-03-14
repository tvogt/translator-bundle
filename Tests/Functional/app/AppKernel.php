<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class AppKernel extends Kernel {
    /**
     * @return array
     */
    public function registerBundles() {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),

            new Calitarus\TranslatorBundle\CalitarusTranslatorBundle(),
        );

        return $bundles;
    }


    /**
     * @param \Symfony\Component\Config\Loader\LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader) {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * @return string
     */
    public function getCacheDir() {
        return sys_get_temp_dir().'/CalitarusTranslatorBundle/cache';
    }

    /**
     * @return string
     */
    public function getLogDir() {
        return sys_get_temp_dir().'/CalitarusTranslatorBundle/logs';
    }
}