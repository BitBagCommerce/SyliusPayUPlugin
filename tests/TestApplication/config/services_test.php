<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if (!str_starts_with((string) ($_SERVER['APP_ENV'] ?? 'prod'), 'test')) {
        return;
    }

    $containerConfigurator->import('../../Behat/Resources/services.xml');
    $containerConfigurator->import(
        '../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services.xml',
    );
};
