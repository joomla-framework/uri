# URI package

TODO

## Rendering the user info

`toString()` and `render()` end the user info with `@` whenever the user or the password is
rendered, also when only one of the two is set or requested:

```php
$uri = new Uri('https://user:secret@example.com/path');

echo $uri->toString(['scheme', 'user', 'host', 'path']); // https://user@example.com/path

echo (new Uri('https://example.com/path'))->setPass('secret'); // https://:secret@example.com/path
```

Parts with the value `'0'`, such as the fragment of `https://example.com/path#0`, are rendered like
any other non-empty value.
