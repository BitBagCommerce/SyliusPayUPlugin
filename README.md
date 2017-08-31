![BitBag](https://bitbag.pl/static/bitbag-logo.png)

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
        
        new \BitBag\PayUPlugin\BitBagPayUPlugin(),
    ]);
}
```

## Testing

In order to run tests, execute following commands:

```bash
$ vendor/bin/phpspec
```
## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/
