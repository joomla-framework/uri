<?php
/**
 * Part of the Joomla Framework Uri Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri;

use Psr\Http\Message\UriInterface as PsrInterface;

/**
 * UriImmutable Class
 *
 * This is an immutable version of the AbstractUri class.
 *
 * @since  1.0
 */
final class UriImmutable extends AbstractUri implements PsrInterface
{
	/**
	 * Flag if the class been instantiated
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	private $constructed = false;

	/**
	 * Prevent setting undeclared properties.
	 *
	 * @param   string  $name   This is an immutable object, setting $name is not allowed.
	 * @param   mixed   $value  This is an immutable object, setting $value is not allowed.
	 *
	 * @return  null  This method always throws an exception.
	 *
	 * @since   1.0
	 * @throws  \BadMethodCallException
	 */
	public function __set($name, $value)
	{
		throw new \BadMethodCallException('This is an immutable object');
	}

	/**
	 * This is a special constructor that prevents calling the __construct method again.
	 *
	 * @param   string  $uri  The optional URI string
	 *
	 * @since   1.0
	 * @throws  \BadMethodCallException
	 */
	public function __construct($uri = null)
	{
		if ($this->constructed === true)
		{
			throw new \BadMethodCallException('This is an immutable object');
		}

		$this->constructed = true;

		parent::__construct($uri);
	}

	/**
	 * Return an instance with the specified scheme.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified scheme.
	 *
	 * Implementations MUST support the schemes "http" and "https" case
	 * insensitively, and MAY accommodate other schemes if required.
	 *
	 * An empty scheme is equivalent to removing the scheme.
	 *
	 * @param   string  $scheme  The scheme to use with the new instance.
	 *
	 * @return  self  A new instance with the specified scheme.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @throws \InvalidArgumentException for invalid or unsupported schemes.
	 */
	public function withScheme($scheme)
	{
		$scheme = strtolower($scheme);
		$new    = clone $this;

		if ($scheme === $this->getScheme())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->scheme = $scheme;

		return $new;
	}

	/**
	 * Return an instance with the specified user information.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified user information.
	 *
	 * Password is optional, but the user information MUST include the
	 * user; an empty string for the user is equivalent to removing user
	 * information.
	 *
	 * @param   string       $user      The user name to use for authority.
	 * @param   null|string  $password  The password associated with $user.
	 *
	 * @return  self  A new instance with the specified user information.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function withUserInfo($user, $password = null)
	{
		$userInfo = $user;

		if ($password)
		{
			$userInfo .= ':' . $password;
		}

		$new = clone $this;

		if ($userInfo === $this->getUserInfo())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->user = $user;
		$new->pass = $password;

		return $new;
	}

	/**
	 * Return an instance with the specified host.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified host.
	 *
	 * An empty host value is equivalent to removing the host.
	 *
	 * @param   string  $host  The hostname to use with the new instance.
	 *
	 * @return  self  A new instance with the specified host.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @throws \InvalidArgumentException for invalid hostnames.
	 */
	public function withHost($host)
	{
		$new = clone $this;

		if ($host === $this->getHost())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->host = $host;

		return $new;
	}

	/**
	 * Return an instance with the specified port.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified port.
	 *
	 * Implementations MUST raise an exception for ports outside the
	 * established TCP and UDP port ranges.
	 *
	 * A null value provided for the port is equivalent to removing the port
	 * information.
	 *
	 * @param   null|int  $port  The port to use with the new instance; a null value
	 *                           removes the port information.
	 *
	 * @return  self  A new instance with the specified port.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @throws \InvalidArgumentException for invalid ports.
	 */
	public function withPort($port)
	{
		if (!is_numeric($port))
		{
			throw new InvalidArgumentException(sprintf(
				'Invalid port specified - you must supply an integer'
			));
		}

		$port = (int) $port;

		if ($port < 1 || $port > 65535)
		{
			throw new InvalidArgumentException(sprintf(
				'Invalid port "%d" specified - you must supply a valid TCP/UDP port',
				$port
			));
		}

		$new  = clone $this;

		if ($port === $this->getPort())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->port = $port;

		return $new;
	}

	/**
	 * Return an instance with the specified path.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified path.
	 *
	 * The path can either be empty or absolute (starting with a slash) or
	 * rootless (not starting with a slash). Implementations MUST support all
	 * three syntaxes.
	 *
	 * If the path is intended to be domain-relative rather than path relative then
	 * it must begin with a slash ("/"). Paths not starting with a slash ("/")
	 * are assumed to be relative to some base path known to the application or
	 * consumer.
	 *
	 * Users can provide both encoded and decoded path characters.
	 * Implementations ensure the correct encoding as outlined in getPath().
	 *
	 * @param   string  $path  The path to use with the new instance.
	 *
	 * @return  self  A new instance with the specified path.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @throws \InvalidArgumentException for invalid paths.
	 */
	public function withPath($path)
	{
		if (!is_string($path))
		{
			throw new InvalidArgumentException(
				'Invalid path provided - you must supply a string'
			);
		}

		if (strpos($path, '?') !== false)
		{
			throw new InvalidArgumentException(
				'Invalid path provided; must not contain a query string'
			);
		}

		if (strpos($path, '#') !== false) {
			throw new InvalidArgumentException(
				'Invalid path provided; must not contain a URI fragment'
			);
		}

		$new = clone $this;

		if ($path === $this->getPath())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->path = $path;

		return $new;
	}

	/**
	 * Return an instance with the specified query string.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified query string.
	 *
	 * Users can provide both encoded and decoded query characters.
	 * Implementations ensure the correct encoding as outlined in getQuery().
	 *
	 * An empty query string value is equivalent to removing the query string.
	 *
	 * @param   string  $query  The query string to use with the new instance.
	 *
	 * @return  self  A new instance with the specified query string.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @throws \InvalidArgumentException for invalid query strings.
	 */
	public function withQuery($query)
	{
		if (!is_string($query))
		{
			throw new InvalidArgumentException(
				'Query string must be a string'
			);
		}

		if (strpos($query, '#') !== false)
		{
			throw new InvalidArgumentException(
				'Query string must not include a URI fragment'
			);
		}

		$new = clone $this;

		if ($query === $this->getQuery())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->query = $query;

		return $new;
	}

	/**
	 * Return an instance with the specified URI fragment.
	 *
	 * This method MUST retain the state of the current instance, and return
	 * an instance that contains the specified URI fragment.
	 *
	 * Users can provide both encoded and decoded fragment characters.
	 * Implementations ensure the correct encoding as outlined in getFragment().
	 *
	 * An empty fragment value is equivalent to removing the fragment.
	 *
	 * @param   string  $fragment  The fragment to use with the new instance.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @return  self  A new instance with the specified fragment.
	 */
	public function withFragment($fragment)
	{
		$new = clone $this;

		if ($fragment === $this->getFragment())
		{
			// Do nothing if no change was made.
			return $new;
		}

		$new->fragment = $fragment;

		return $new;
	}
}
