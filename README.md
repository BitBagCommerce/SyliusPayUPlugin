# [![](https://bitbag.io/wp-content/uploads/2020/10/payu.png)](https://bitbag.io/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 

# PayU Payments Plugin for Sylius
----

[![](https://img.shields.io/packagist/l/bitbag/payu-plugin.svg) ](https://packagist.org/packages/bitbag/payu-plugin "License") [ ![](https://img.shields.io/packagist/v/bitbag/payu-plugin.svg) ](https://packagist.org/packages/bitbag/payu-plugin "Version") [ ![](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin.svg?branch=master) ](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin "Build status") [![](https://poser.pugx.org/bitbag/payu-plugin/downloads)](https://packagist.org/packages/bitbag/payu-plugin "Total Downloads") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu)

At BitBag we do believe in open source. However, we are able to do it just because of our awesome clients, who are kind enough to share some parts of our work with the community. Therefore, if you feel like there is a possibility for us working together, feel free to reach us out. You will find out more about our professional services, technologies and contact details at [https://bitbag.io/](https://bitbag.io/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu).

## Table of Content
---
* [Overwiev](#overwiev)
* [Installation](#installation)
  * [Requirements](#requirements)
  * [Customization](#customization)
  * [Testing](#testing)
* [About us](#about-us)
  * [Community](#community)
* [Demo Sylius shop](#demo-sylius-shop)
* [Additional resources for developers](#additional-resources-for-developers)
* [License](#license)
* [Contact](#contact)


# Overwiev
---
The plugin integrates [PayU Poland payments](https://www.payu.pl/) with Sylius based applications. After the installation you should be able to create a payment method for PayU gateway and enable its payments in your web store.


## Note

PayU operates the payment service provider service in various countries under the same brand ([RO](https://www.payu.ro/), [PL](https://www.payu.pl/), [IN](https://www.payu.in/), [AR](https://www.payulatam.com/ar/), [BR](https://www.payu.com.br/) just to name a few). Unfortunately, they use different platforms and this plugin it does not work for PayU in Romania, for example. 

### We are here to help
This **open-source plugin was developed to help the Sylius community** and make PayU payment solution available to any Sylius store. If you have any additional questions, would like help with installing or configuring the plugin or need any assistance with your Sylius project - let us know!

[![](https://bitbag.io/wp-content/uploads/2020/10/button-contact.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 


# Installation
---

## Requirements

We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.  

| Package       | Version        |
|:-------------:|:--------------:|
| PHP           |  ^7.4  |
| Sylius      |  ^1.10 |
---

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
----

### Available services you can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) and forms you can [extend](http://symfony.com/doc/current/form/create_form_type_extension.html)

Run the below command to see what Symfony services are shared with this plugin:
```bash
$ bin/console debug:container bitbag.payu_plugin
```

## Testing
----

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


# About us
---

BitBag is an agency that provides high-quality **eCommerce and Digital Experience software**. Our main area of expertise includes eCommerce consulting and development for B2C, B2B, and Multi-vendor Marketplaces. 
The scope of our services related to Sylius includes:
- **Consulting** in the field of strategy development
- Personalized **headless software development**
- **System maintenance and long-term support**
- **Outsourcing**
- **Plugin development**
- **Data migration**

Some numbers regarding Sylius:
* **20+ experts** including consultants, UI/UX designers, Sylius trained front-end and back-end developers,
* **100+ projects** delivered on top of Sylius,
* Clients from over **20+ countries** 
* **3+ years** in the Sylius ecosystem.

---

If you need some help with Sylius development, don't be hesitate to contact us directly. You can fill the form on [this site](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) or send us an e-mail to hello@bitbag.io!

---

[![](https://bitbag.io/wp-content/uploads/2020/10/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 

## Community
----

For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/). 

# Demo Sylius shop
---

We created a demo app with some useful use-cases of plugins!
Visit b2b.bitbag.shop to take a look at it. The admin can be accessed under https://b2b.bitbag.shop/admin/login link and sylius: sylius credentials.
Plugins that we have used in the demo:
| BitBag's Plugin | GitHub | Sylius' Store|
| ------ | ------ | ------|
| ACL PLugin | *Private. Available after the purchasing.*| https://plugins.sylius.com/plugin/access-control-layer-plugin/| 
| Braintree Plugin | https://github.com/BitBagCommerce/SyliusBraintreePlugin |https://plugins.sylius.com/plugin/braintree-plugin/|
| CMS Plugin | https://github.com/BitBagCommerce/SyliusCmsPlugin | https://plugins.sylius.com/plugin/cmsplugin/|
| Elasticsearch Plugin | https://github.com/BitBagCommerce/SyliusElasticsearchPlugin | https://plugins.sylius.com/plugin/2004/|
| Mailchimp Plugin | https://github.com/BitBagCommerce/SyliusMailChimpPlugin | https://plugins.sylius.com/plugin/mailchimp/ |
| Multisafepay Plugin | https://github.com/BitBagCommerce/SyliusMultiSafepayPlugin |
| Wishlist Plugin | https://github.com/BitBagCommerce/SyliusWishlistPlugin | https://plugins.sylius.com/plugin/wishlist-plugin/|
| **Sylius' Plugin** | **GitHub** | **Sylius' Store** |
| Admin Order Creation Plugin | https://github.com/Sylius/AdminOrderCreationPlugin | https://plugins.sylius.com/plugin/admin-order-creation-plugin/ |
| Invoicing Plugin | https://github.com/Sylius/InvoicingPlugin | https://plugins.sylius.com/plugin/invoicing-plugin/ |
| Refund Plugin | https://github.com/Sylius/RefundPlugin | https://plugins.sylius.com/plugin/refund-plugin/ |

**If you need an overview of Sylius' capabilities, schedule a consultation with our expert.**

[![](https://bitbag.io/wp-content/uploads/2020/10/button_free_consulatation-1.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 


## Additional resources for developers
---
To learn more about our contribution workflow and more, we encourage ypu to use the following resources:  
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)


   
## License
 ---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.) 

## Contact
---
If you want to contact us, the best way is to fill the form on  [our website](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) or send us an e-mail to hello@bitbag.io with your question(s). We guarantee that we answer as soon as we can! 

[![](https://bitbag.io/wp-content/uploads/2020/10/footer.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 
