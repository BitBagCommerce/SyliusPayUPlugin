# [![](https://bitbag.io/wp-content/uploads/2020/10/payu.png)](https://bitbag.io/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 

# BitBag SyliusPayUPlugin
----

[![](https://img.shields.io/packagist/l/bitbag/payu-plugin.svg) ](https://packagist.org/packages/bitbag/payu-plugin "License") [ ![](https://img.shields.io/packagist/v/bitbag/payu-plugin.svg) ](https://packagist.org/packages/bitbag/payu-plugin "Version") [ ![](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin.svg?branch=master) ](https://travis-ci.org/BitBagCommerce/SyliusPayUPlugin "Build status") [![](https://poser.pugx.org/bitbag/payu-plugin/downloads)](https://packagist.org/packages/bitbag/payu-plugin "Total Downloads") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu)

<p>
 <img align="left" src="https://sylius.com/assets/badge-approved-by-sylius.png" width="85">
</p> 

We want to impact many unique eCommerce projects and build our brand recognition worldwide, so we are heavily involved in creating open-source solutions, especially for Sylius. We have already created over **35 extensions, which have been downloaded almost 2 million times.**

You can find more information about our eCommerce services and technologies on our website: https://bitbag.io/. We have also created a unique service dedicated to creating plugins: https://bitbag.io/services/sylius-plugin-development. 

Do you like our work? Would you like to join us? Check out the **“Career” tab:** https://bitbag.io/pl/kariera. 

# About Us 
---

BitBag is a software house that implements tailor-made eCommerce platforms with the entire infrastructure—from creating eCommerce platforms to implementing PIM and CMS systems to developing custom eCommerce applications, specialist B2B solutions, and migrations from other platforms.

We actively participate in Sylius's development. We have already completed **over 150 projects**, cooperating with clients worldwide, including smaller enterprises and large international companies. We have completed projects for such important brands as **Mytheresa, Foodspring, Planeta Huerto (Carrefour Group), Albeco, Mollie, and ArtNight.**

We have a 70-person team of experts: business analysts and consultants, eCommerce developers, project managers, and QA testers.

**Our services:**
* B2B and B2C eCommerce platform implementations
* Multi-vendor marketplace platform implementations
* eCommerce migrations
* Sylius plugin development
* Sylius consulting
* Project maintenance and long-term support
* PIM and CMS implementations

**Some numbers from BitBag regarding Sylius:**
* 70 experts on board 
* +150 projects delivered on top of Sylius
* 30 countries of BitBag’s customers
* 7 years in the Sylius ecosystem
* +35 plugins created for Sylius

---
[![](https://bitbag.io/wp-content/uploads/2024/09/badges-sylius.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu) 

---

## Table of Content
---
* [Overview](#overview)
* [Functionalities](#functionalities)
* [Installation](#installation)
  * [Requirements](#requirements)
  * [Customization](#customization)
  * [Testing](#testing)
* [Functionalities](#functionalities)
* [Demo](#demo)
* [License](#license)
* [Contact and Support](#contact-and-support)
* [Community](#community)


# Overview
---
The plugin integrates [PayU Poland payments](https://www.payu.pl/) with Sylius-based applications. The PayU Plugin fosters growth and facilitates secure and smooth transactions for web stores operating in Poland. It embodies a blend of reliability, user-friendliness, and adaptability, making it a valuable addition to your Sylius-powered online store.  After the installation, you should be able to create a payment method for the PayU gateway and enable its payments in your web store.

## Note

PayU operates the payment service provider service in various countries under the same brand ([RO](https://www.payu.ro/), [PL](https://www.payu.pl/), [IN](https://www.payu.in/), [AR](https://www.payulatam.com/ar/), [BR](https://www.payu.com.br/) just to name a few). Unfortunately, they use different platforms and this plugin it does not work for PayU in Romania, for example. 


# Installation
---
For the full installation guide, please go [here](doc/installation.md).  

## Requirements
---

We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>=8.0          |
| sylius/sylius | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | 14.x            |


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
$ yarn encore dev
$ php bin/console sylius:install --env test
$ php bin/console server:start --env test
$ open http://localhost:8000
$ bin/behat features/*
$ bin/phpspec run
```
# Functionalities
---
All main functionalities of the plugin are described [here.](https://github.com/BitBagCommerce/SyliusPayUPlugin/blob/master/doc/functionalities.md)

# Demo 
---

We created a demo app with some useful use-cases of plugins! Visit http://demo.sylius.com/ to take a look at it.

**If you need an overview of Sylius' capabilities, schedule a consultation with our expert.**

[![](https://bitbag.io/wp-content/uploads/2020/10/button_free_consulatation-1.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu)

# Additional resources for developers
---
To learn more about our contribution workflow and more, we encourage you to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)
* [Sylius Plugins Blogs](https://bitbag.io/blog/category/plugins)


# License
---

This plugin's source code is completely free and released under the terms of the MIT license.

[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen.)

# Contact and Support 
---
This open-source plugin was developed to help the Sylius community. If you have any additional questions, would like help with installing or configuring the plugin, or need any assistance with your Sylius project - let us know! **Contact us** or send us an **e-mail to hello@bitbag.io** with your question(s).

[![](https://bitbag.io/wp-content/uploads/2020/10/button-contact.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu)


# Community
----

For online communication, we invite you to chat with us & other users on **[Sylius Slack](https://sylius-devs.slack.com/).**

[![](https://bitbag.io/wp-content/uploads/2024/09/badges-partners.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_payu)
