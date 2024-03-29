## The Uri Package [![Build Status](https://ci.joomla.org/api/badges/joomla-framework/uri/status.svg?ref=refs/heads/2.0-dev)](https://ci.joomla.org/joomla-framework/uri)

[![Latest Stable Version](https://poser.pugx.org/joomla/uri/v/stable)](https://packagist.org/packages/joomla/uri)
[![Total Downloads](https://poser.pugx.org/joomla/uri/downloads)](https://packagist.org/packages/joomla/uri)
[![Latest Unstable Version](https://poser.pugx.org/joomla/uri/v/unstable)](https://packagist.org/packages/joomla/uri)
[![License](https://poser.pugx.org/joomla/uri/license)](https://packagist.org/packages/joomla/uri)

### Introduction

The Joomla Framework includes a Uri package that allows for manipulating pieces of the Uri string with a number of useful methods to set and get values while dealing with the uri.

The classes that are included with the Uri package are `Uri`, which extends the `UriAbstract` class, an implementation of the `UriInterface`. Another class is the `UriHelper`class.

The Uri class is a mutable object which you'd use to manipulate an Uri.

To pass along an uri as value use `UriImmutable`, this object guarantees that the code you pass the object into can't manipulate it and, causing bugs in your code.

If only read access is required it's recommended to type hint against the `UriInterface`. This way either an `Uri` or an `UriImmutable` object can be passed.

The `UriHelper` class only contains one method parse_url() that's an UTF-8 safe replacement for PHP's parse_url().

You can use the `Uri` class a number of different ways when dealing with Uris. It is very easy to construct a uri programmatically using the methods provided in the `Uri` class.


### Usage

The methods provided in the `Uri` class allow you to manipulate all aspects of a uri. For example, suppose you wanted to set a new uri, add in a port, and then also post a username and password to authenticate a .htaccess security file. You could use the following syntax:

```php
<?php
// new uri object
$uri = new Joomla\Uri\Uri;

$uri->setHost('http://localhost');
$uri->setPort('8888');
$uri->setUser('myUser');
$uri->setPass('myPass');

echo $uri->__toString();
```
This will output:

`myUser:myPass@http://localhost:8888`

If you wanted to add a specific filepath after the host you could use the `setPath()` method:

```php
<?php
// set path
$uri->setPath('path/to/file.php');
```

Which will output
   myUser:myPass@http://localhost:8888path/to/file.php

Adding a URL query:
```php
<?php
// url query
$uri->setQuery('foo=bar');
```

Output:

`myUser:myPass@http://localhost:8888path/to/file.php?foo=bar`

## Installation via Composer

Add `"joomla/uri": "~2.0"` to the require block in your composer.json and then run `composer install`.

```json
{
	"require": {
		"joomla/uri": "~2.0"
	}
}
```

Alternatively, you can simply run the following from the command line:

```sh
composer require joomla/uri "~2.0"
```
