## Updating from v4 to v5

The following changes were made to the URI package between v4 and v5.

### Native parameter, return and property types

The URI classes now declare native types instead of documenting them in doc blocks only. Getters
for parts that can be absent are nullable. Classes implementing `Joomla\Uri\UriInterface` or extending
`AbstractUri`/`Uri` must add the same types to the methods and properties they override, otherwise
PHP raises a fatal error when the class is loaded. Passing a value of the wrong type (for example
`setPort('')`) now raises a `TypeError`; numeric strings such as `'8080'` are still accepted for
`setPort()` unless the calling file uses `strict_types`.

| Class | Member | v4 | v5 |
|---|---|---|---|
| `UriInterface`, `AbstractUri` | `__toString()` | `__toString()` | `__toString(): string` |
| `UriInterface`, `AbstractUri` | `toString()` | `toString($parts = [...])` | `toString(array $parts = [...]): string` |
| `UriInterface`, `AbstractUri` | `hasVar()` | `hasVar($name)` | `hasVar(string $name): bool` |
| `UriInterface`, `AbstractUri` | `getVar()` | `getVar($name, $default = null)` | `getVar(string $name, mixed $default = null): mixed` |
| `UriInterface`, `AbstractUri` | `getQuery()` | `getQuery($toArray = false)` | `getQuery(bool $toArray = false): array\|string` |
| `UriInterface`, `AbstractUri` | `getScheme()`, `getUser()`, `getPass()`, `getHost()`, `getPath()`, `getFragment()` | no return type | `: ?string` |
| `UriInterface`, `AbstractUri` | `getPort()` | no return type | `: ?int` |
| `UriInterface`, `AbstractUri` | `isSsl()` | `isSsl()` | `isSsl(): bool` |
| `AbstractUri` | `__construct()` | `__construct($uri = null)` | `__construct(?string $uri = null)` |
| `AbstractUri` | `render()` | `render($parts = self::ALL)` | `render(int $parts = self::ALL): string` |
| `AbstractUri` | `buildQuery()` | `buildQuery(array $params)` | `buildQuery(array $params): string` |
| `AbstractUri` | `parse()` | `parse($uri)` | `parse(string $uri): bool` |
| `AbstractUri` | `cleanPath()` | `cleanPath($path)` | `cleanPath(string $path): string` |
| `AbstractUri` | `$uri`, `$scheme`, `$host`, `$user`, `$pass`, `$path`, `$query`, `$fragment` | untyped | `?string` |
| `AbstractUri` | `$port` | untyped | `?int` |
| `AbstractUri` | `$vars` | untyped | `array` |
| `Uri` | `setVar()` | `setVar($name, $value)` | `setVar(string $name, mixed $value): mixed` |
| `Uri` | `delVar()` | `delVar($name)` | `delVar(string $name): void` |
| `Uri` | `setQuery()` | `setQuery($query)` | `setQuery(array\|string $query): void` |
| `Uri` | `setScheme()`, `setUser()`, `setPass()`, `setHost()`, `setFragment()` | untyped parameter | `?string` parameter, `: static` |
| `Uri` | `setPort()` | `setPort($port)` | `setPort(?int $port): static` |
| `Uri` | `setPath()` | `setPath($path)` | `setPath(string $path): static` |
| `UriImmutable` | `__construct()` | `__construct($uri = null)` | `__construct(?string $uri = null)` |
| `UriImmutable` | `__set()` | `__set($name, $value)` | `__set(string $name, mixed $value): void` |
| `UriHelper` | `parse_url()` | `parse_url($url, $component = -1)` | `parse_url(string $url, int $component = -1): array\|string\|int\|false\|null` |

`UriInterface::toString()` declares `array $parts` again (the type was dropped in 3.0). An
implementation of the interface that still has an untyped `$parts` stays compatible.
