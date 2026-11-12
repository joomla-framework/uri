# URI package

TODO

## Encoding of the query string

When a query variable is changed with `setVar()`, `delVar()` or `setQuery()`, the query string is
rebuilt from the variables. By default the rebuilt query is **not** percent-encoded, so a value
containing a reserved character such as `&`, `=` or `#` changes the structure of the URI, and values
that were encoded in the original URI are decoded:

```php
$uri = new Uri('https://example.com/index.php?q=a%26b');
$uri->setVar('name', 'x&admin=1');

echo $uri; // https://example.com/index.php?q=a&b&name=x&admin=1
```

`Uri::setQueryEncoding()` switches to percent-encoding as defined in RFC 3986:

```php
$uri = (new Uri('https://example.com/index.php?q=a%26b'))->setQueryEncoding();
$uri->setVar('name', 'x&admin=1');

echo $uri; // https://example.com/index.php?q=a%26b&name=x%26admin%3D1
```

Building an unencoded query is deprecated and triggers a deprecation notice whenever the result
differs from the encoded one. As of 5.0 the query is always encoded. Code that encodes values itself
before passing them to `setVar()` has to stop doing so when it enables the encoding.
