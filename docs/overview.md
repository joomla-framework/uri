# URI package

TODO

## Encoding of the query string

When a query variable is changed with `setVar()`, `delVar()` or `setQuery()`, the query string is
rebuilt from the variables. Names and values are percent-encoded as defined in RFC 3986, so values
may contain any character:

```php
$uri = new Uri('https://example.com/index.php?q=a%26b');
$uri->setVar('name', 'x&admin=1');

echo $uri; // https://example.com/index.php?q=a%26b&name=x%26admin%3D1
```

Pass values unencoded; `getVar()` returns them decoded.
