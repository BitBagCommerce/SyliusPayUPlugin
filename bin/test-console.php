<?php

declare(strict_types=1);

use Sylius\TestApplication\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/../vendor/sylius/test-application/.env');
$pluginDir = dirname(__DIR__);
if (file_exists($pluginEnvPath = $pluginDir . '/tests/TestApplication/.env')) {
    (new Dotenv())->bootEnv($pluginEnvPath);
    $_SERVER['APP_CACHE_DIR'] = $_SERVER['APP_CACHE_DIR'] ?? $pluginDir . '/var/cache';
    $_SERVER['APP_LOG_DIR'] = $_SERVER['APP_LOG_DIR'] ?? $pluginDir . '/var/log';
}
$_SERVER['APP_ENV'] = 'test';

$kernel = new Kernel('test', false);
$app = new Application($kernel);
$app->run();
