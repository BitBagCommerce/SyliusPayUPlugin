<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return array_merge(parent::registerBundles(), [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(), // Required by SyliusApiBundle
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),

            new BitBag\PayUPlugin\BitBagPayUPlugin(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config.yml');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerBaseClass()
    {
        if ('test' === $this->environment) {
            return MockerContainer::class;
        }
        return parent::getContainerBaseClass();
    }
}
