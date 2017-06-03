# Sylius PayU payment gateway plugin  

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
$ vendor/bin/phpspec
```