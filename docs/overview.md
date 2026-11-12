# URI package

TODO

## Deriving a modified `UriImmutable`

`UriImmutable` cannot be changed after construction. Its `with*()` methods return a modified copy
and leave the original untouched. They follow the naming of PSR-7's `UriInterface`:

```php
use Joomla\Uri\UriImmutable;

$uri  = new UriImmutable('http://example.com/path?a=1');
$copy = $uri->withScheme('https')
    ->withUserInfo('user', 'secret')   // withUserInfo('') removes the user info
    ->withHost('example.org')
    ->withPort(8443)                   // null removes the port
    ->withPath('/other/./path')        // dot segments are resolved like in Uri::setPath()
    ->withQuery('b=2')                 // an encoded query string, used as it is, or an array
    ->withFragment('top');

echo $copy; // https://user:secret@example.org:8443/other/path?b=2#top
echo $uri;  // http://example.com/path?a=1
```
