## Updating from v4 to v5

The following changes were made to the URI package between v4 and v5.

### The query string is percent-encoded

When a query variable is changed with `setVar()`, `delVar()` or `setQuery()`, the query string is
rebuilt from the variables. In 4.x the rebuilt query was not encoded (`AbstractUri::buildQuery()`
ran `urldecode()` over the result), so values containing `&`, `=` or `#` changed the structure of
the URI and encoded values of the original URI were decoded. 5.0 encodes names and values as
defined in RFC 3986:

```php
$uri = new Uri('https://example.com/index.php?q=a%26b');
$uri->setVar('name', 'x&admin=1');

echo $uri;
// 4.x: https://example.com/index.php?q=a&b&name=x&admin=1
// 5.0: https://example.com/index.php?q=a%26b&name=x%26admin%3D1
```

Code that encoded values itself before passing them to `setVar()` or `setQuery()` (to work around
the decoding) must stop doing so, or the values are encoded twice. In 4.x the new behaviour can be
enabled per instance with `Uri::setQueryEncoding()`; relying on the decoding is deprecated there.

Spaces are now encoded as `%20` and brackets of array variables as `%5B`/`%5D`
(`a%5B0%5D=1`), which every server decodes like the unencoded form.
