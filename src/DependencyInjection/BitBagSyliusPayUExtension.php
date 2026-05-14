<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class BitBagSyliusPayUExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $gatewayConfigTemplate = '@BitBagSyliusPayUPlugin/Admin/PaymentMethod/gatewayConfiguration.html.twig';
        $hookConfig = [
            'hooks' => [
                'sylius_admin.payment_method.create.content.form.sections.gateway_configuration.payu' => [
                    'config' => ['template' => $gatewayConfigTemplate, 'priority' => 100],
                ],
                'sylius_admin.payment_method.update.content.form.sections.gateway_configuration.payu' => [
                    'config' => ['template' => $gatewayConfigTemplate, 'priority' => 100],
                ],
            ],
        ];

        $container->prependExtensionConfig('sylius_twig_hooks', $hookConfig);
    }
}
