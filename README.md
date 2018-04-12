![BitBag](https://bitbag.pl/static/bitbag-logo.png)

# BitBag PayU Plugin [![Build Status](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin.svg?branch=master)](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin)

## Overview

This plugin integrated [PayU payments](https://www.payu.pl/) with Sylius based applications. After installing it you should be able to create a payment method for PayU gateway and enable its payments in your web store.

## Support

Do you want us to customize this plugin for your specific needs? Write us an email on mikolaj.krol@bitbag.pl 💻

## Installation

```bash
$ composer require bitbag/payu-plugin

```
    
Add plugin dependencies to your AppKernel.php file:

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \BitBag\SyliusPayUPlugin\BitBagSyliusPayUPlugin(),
    ]);
}
```

## Testing
```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install
$ yarn install
$ yarn run gulp
$ php bin/console sylius:install --env test
$ php bin/console server:start --env test
$ open http://localhost:8000
$ bin/behat features/*
$ bin/phpspec run
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
