# Installation

## Overview:
GENERAL
<!------------------------------------------------------------------
Downloading the plugin
Importing or modifying config files, adding routes, parameters, services etc.
-------------------------------------------------------------------->
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
ADDITIONAL
- [Known Issues](#known-issues)
---

## Composer:
```bash
composer require bitbag/payu-plugin
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    BitBag\SyliusPayUPlugin\BitBagSyliusPayUPlugin::class => ['all' => true],
];
```

Import plugin configuration in `config/packages/_sylius.yaml` file:
```yaml
    # config/packages/_sylius.yaml

    imports:
    # ...
    - { resource: "@BitBagSyliusPayUPlugin/config/config.yaml" }
```

### Clear application cache by using command:
```bash
bin/console cache:clear
```
**Note:** If you are running it on production, add the `-e prod` flag to this command.

## Known issues

> For the plugin to work correctly, `base currency` should be set to `Polish Zloty` in the Sylius admin panel.

### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
