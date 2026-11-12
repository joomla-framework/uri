# URI package

TODO

## `UriHelper::parse_url()`

A UTF-8 safe wrapper around PHP's `parse_url()` with the same signature and return values. The
second argument retrieves a single component, also for URLs with non-ASCII characters, and the port
is always returned as an integer:

```php
use Joomla\Uri\UriHelper;

UriHelper::parse_url('https://bücher.de:8080/über', PHP_URL_HOST); // 'bücher.de'
UriHelper::parse_url('https://bücher.de:8080/über', PHP_URL_PORT); // 8080
```
