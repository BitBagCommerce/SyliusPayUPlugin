# Sylius MailChimpPlugin 

## Installation

```bash
$ composer require bitbag/mailchimp-plugin
```
    
Add plugin dependencies to your AppKernel.php file:

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \BitBag\PayUPlugin\PayUPlugin(),
    ]);
}
```
 
## Testing & Development

In order to run tests, execute following commands:

```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console doctrine:database:create --env test
$ bin/console doctrine:schema:create --env test
$ vendor/bin/phpspec
```