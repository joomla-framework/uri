# URI package

TODO

## Query variable names

The query string is parsed into variables when a URI is parsed and by `setQuery()`. Unlike PHP's
`parse_str()`, dots and spaces in variable names are kept, so the names can be read as they appear in
the URI and the query is rebuilt unchanged:

```php
$uri = new Uri('https://example.com/?a.b=1&c+d=2');

$uri->getQuery(true); // ['a.b' => '1', 'c d' => '2']
$uri->getVar('a.b');  // '1'
```

Array notation (`a[]=1&a[]=2`, `a[b]=1`) is parsed like `parse_str()` does. A variable that is
repeated without `[]` keeps only its last value.
