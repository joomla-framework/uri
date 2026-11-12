# URI package

TODO

## Selecting the parts to render

`toString()` takes the names of the parts to render: `scheme`, `user`, `pass`, `host`, `port`,
`path`, `query` and `fragment` (or `all`). Passing any other name, for example a typo such as
`hosts`, is deprecated and triggers a deprecation notice; as of 5.0 it throws an
`InvalidArgumentException`.

```php
$uri->toString(['scheme', 'host', 'path']);
```
