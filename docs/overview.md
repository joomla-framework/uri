# Overview

The URI package parses a URI into its parts, gives you typed access to them, and renders it back
to a string. It also carries the query string as a keyed array so individual variables can be read
and changed.

```bash
composer require joomla/uri
```

## What is in the package

| Class | Purpose |
|---|---|
| `Joomla\Uri\UriInterface` | Read-only access to a URI, plus the part constants |
| `Joomla\Uri\AbstractUri` | Parsing, accessors, rendering |
| `Joomla\Uri\Uri` | Adds the setters — the class you normally use |
| `Joomla\Uri\UriImmutable` | Parses once and refuses further construction |
| `Joomla\Uri\UriHelper` | A UTF-8 tolerant `parse_url()` |

## Reading a URI

```php
use Joomla\Uri\Uri;

$uri = new Uri('https://user:secret@example.com:8080/path/to/page?foo=bar&baz=1#section');

$uri->getScheme();     // 'https'
$uri->getUser();       // 'user'
$uri->getPass();       // 'secret'
$uri->getHost();       // 'example.com'
$uri->getPort();       // 8080
$uri->getPath();       // '/path/to/page'
$uri->getQuery();      // 'foo=bar&baz=1'
$uri->getFragment();   // 'section'
$uri->isSsl();         // true
```

Query variables are also available individually:

```php
$uri->getQuery(true);          // ['foo' => 'bar', 'baz' => '1']
$uri->hasVar('foo');           // true
$uri->getVar('missing', '0');  // '0'
```

## Changing a URI

`Uri` adds setters, all of which return `$this`:

```php
$uri = new Uri('https://example.com/index.php');

$uri->setVar('page', 2)
    ->setPath('/articles')
    ->setFragment('top');

$uri->delVar('page');
$uri->setQuery(['a' => 1, 'b' => 2]);      // array or string

echo $uri;                                  // __toString() calls toString()
```

## Rendering

`toString()` takes the list of parts to include:

```php
$uri->toString();                                   // everything
$uri->toString(['scheme', 'host', 'path']);         // https://example.com/articles
$uri->toString(['path', 'query']);                  // /articles?a=1&b=2
```

There is also a bitmask form via the constants on `UriInterface` — `SCHEME`, `USER`, `PASS`,
`HOST`, `PORT`, `PATH`, `QUERY`, `FRAGMENT` and `ALL`:

```php
$uri->render(UriInterface::SCHEME | UriInterface::HOST | UriInterface::PATH);
```

An unknown part name in `toString()` is silently ignored, so a typo drops that part without any
error.

## `UriImmutable`

`UriImmutable` extends `AbstractUri`, so it has the getters but none of the setters, and its
constructor refuses to run twice:

```php
$uri = new UriImmutable('https://example.com/');
$uri->__construct('https://elsewhere.test/');   // BadMethodCallException
```

Note that it offers no `with*()` methods, so there is no way to derive a modified copy — you have
to build a new instance from a string.

## `UriHelper::parse_url()`

A wrapper around `parse_url()` that also handles URLs containing non-ASCII characters:

```php
use Joomla\Uri\UriHelper;

$parts = UriHelper::parse_url('https://example.com/über/uns');
```

Two limitations to be aware of:

* The second `$component` argument (`PHP_URL_HOST` and friends) **does not work** on the UTF-8 path.
  `parse_url()` then returns a string, and the helper calls `array_map()` on it, which raises a
  `TypeError`. Use it without the second argument.
* On the UTF-8 path the helper decodes percent-encoding in the result, so `/a%2Fb` comes back as
  `/a/b`. If you rely on the encoded form — for example to reject encoded slashes — do that check
  before parsing.

## Things to know before you build on this

**Query values are not percent-encoded.** `AbstractUri::buildQuery()` runs `urldecode()` over the
output of `http_build_query()`, which undoes the encoding that was just applied:

```php
$uri = new Uri('https://example.com/index.php');
$uri->setVar('name', 'x&admin=1');

echo $uri->toString();
// https://example.com/index.php?name=x&admin=1   ← two parameters, not one
```

Any value that can contain `&`, `#` or `=` therefore has to be encoded by the caller, or the
receiving side will read extra parameters. The same mechanism turns `+` into a space.

**Credentials are part of the default output.** `toString()` and `__toString()` include `user` and
`pass`, so echoing a URI or putting it in a log writes the password in clear text. Pass an explicit
part list when the URI might carry credentials:

```php
$uri->toString(['scheme', 'host', 'port', 'path', 'query']);
```

**A user without a password loses the `@`.** In `render()` the separator is emitted inside the
password branch, so `https://token@example.com/` renders as `https://tokenexample.com/` — a
different host. Set a password as well, or assemble the authority yourself.

**Nothing is validated.** `setScheme('javascript')`, `setHost("evil.test\r\nX: y")` and any other
value are accepted as given and rendered verbatim. If a URI is built from user input and later
used in a `Location` header or an `href`, validate it yourself.

**Paths are not normalised on parse.** `cleanPath()` resolves `.` and `..`, but it only runs in
`setPath()`, not when a URI is parsed. `getPath()` on a parsed URI returns the raw path, dot
segments included.

## Not part of this package

* No PSR-7. `Joomla\Uri\UriInterface` shares its name with `Psr\Http\Message\UriInterface` but is
  a different, incompatible contract, and there is no adapter between them.
* No `with*()` methods, no `resolve()` for relative references, no `equals()`, no normalisation
  and no IDN/punycode conversion.
