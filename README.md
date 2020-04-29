![BitBag](https://bitbag.io/wp-content/uploads/2020/04/logo.svg)

# BitBag PayU Plugin [![Build Status](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin.svg?branch=master)](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin)

## Overview

The plugin integrates [PayU Poland payments](https://www.payu.pl/) with Sylius based applications. After the installation you should be able to create a payment method for PayU gateway and enable its payments in your web store.


## Note

PayU operates the payment service provider service in various countries under the same brand ([RO](https://www.payu.ro/), [PL](https://www.payu.pl/), [IN](https://www.payu.in/), [AR](https://www.payulatam.com/ar/), [BR](https://www.payu.com.br/) just to name a few). Unfortunately, they use different platforms and this plugin it does not work for PayU in Romania, for example. 

## Support

We work on amazing eCommerce projects on top of Sylius and Pimcore. Need some help or additional resources for a project?
Write us an email on mikolaj.krol@bitbag.pl or visit [our website](https://bitbag.shop/)! :rocket:

## Demo

We created a demo app with some useful use-cases of the plugin! Visit [demo.bitbag.shop](https://demo.bitbag.shop) to take a look at it. 
The admin can be accessed under [demo.bitbag.shop/admin](https://demo.bitbag.shop/admin) link and `sylius: sylius` credentials.

## Installation

```bash
$ composer require bitbag/payu-plugin

```
    
Add plugin dependencies to your config/bundles.php file:

```php
return [
    BitBag\SyliusPayUPlugin\BitBagSyliusPayUPlugin::class => ['all' => true],
]
```
## Customization

### Available services you can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) and forms you can [extend](http://symfony.com/doc/current/form/create_form_type_extension.html)

Run the below command to see what Symfony services are shared with this plugin:
```bash
$ bin/console debug:container bitbag.payu_plugin
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

Learn more about our contribution workflow on <http://docs.sylius.org/en/latest/contributing/>.
