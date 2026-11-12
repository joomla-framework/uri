# Updating from v2 to v3

Release 3.0.0 raises the PHP requirement, reformats the codebase and relaxes one type declaration
on `UriInterface`.

## At a glance

| | v2 (2.0.4) | v3 (3.0.0) |
|---|---|---|
| PHP | `^7.2.5 \| ^8.0` | `^8.1.0` |
| `UriInterface::toString()` | `array $parts` | `$parts` — breaking for custom implementations |
| Coding style | Joomla Coding Standard | PSR-12 |

## Minimum supported PHP version raised

All Framework packages now require **PHP 8.1** or newer.

## `UriInterface::toString()` lost its parameter type

```php
// v2
public function toString(array $parts = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment']);

// v3
public function toString($parts = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment']);
```

The `array` type hint was removed from the interface. For callers nothing changes — an array is
still what the method expects, and `AbstractUri::toString()` iterates over `$parts` regardless.

**If you implement `UriInterface` yourself, this is a breaking change.** An untyped interface
parameter means `mixed`, and PHP does not allow an implementation to narrow it back to `array`:

```php
interface UriInterface { public function toString($parts = []); }

class MyUri implements UriInterface {
    public function toString(array $parts = []) { /* … */ }
}

// PHP Fatal error: Declaration of MyUri::toString(array $parts = []) must be
// compatible with UriInterface::toString($parts = [])
```

So an implementation carried over unchanged from 2.x fails to load. Drop the `array` type hint to
match the interface:

```php
// v2
public function toString(array $parts = [...])

// v3
public function toString($parts = [...])
```

Classes extending `AbstractUri` — including `Uri` and `UriImmutable` — are unaffected, since they
inherit the method rather than declaring it.

No other method on `UriInterface`, `AbstractUri`, `Uri`, `UriImmutable` or `UriHelper` changed.

## Codebase converted to PSR-12

The package was reformatted from the Joomla Coding Standard to PSR-12. This touches nearly every
line and changes no behaviour, so a `git diff` between 2.x and 3.x is almost entirely noise. Use
`git diff -w` when looking for real changes.

## Dependency changes

| Package | v2 (2.0.4) | v3 (3.0.0) |
|---|---|---|
| `php` | `^7.2.5 \| ^8.0` | `^8.1.0` |

The package has no runtime dependencies beyond PHP itself.
