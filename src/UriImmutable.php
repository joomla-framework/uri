<?php

/**
 * Part of the Joomla Framework Uri Package
 *
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri;

/**
 * UriImmutable Class
 *
 * This is an immutable version of the AbstractUri class.
 *
 * @since  1.0
 */
final class UriImmutable extends AbstractUri
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
     * @return  void  This method always throws an exception.
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
        if ($this->constructed === true) {
            throw new \BadMethodCallException('This is an immutable object');
        }

        $this->constructed = true;

        parent::__construct($uri);
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param   string  $scheme  The URI scheme.
     *
     * @return  static  A new instance with the specified scheme.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withScheme(string $scheme): static
    {
        return $this->with(['scheme' => $scheme]);
    }

    /**
     * Return an instance with the specified user information.
     *
     * @param   string   $user      The user name.
     * @param   ?string  $password  The password, null to remove it.
     *
     * @return  static  A new instance with the specified user information.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withUserInfo(string $user, ?string $password = null): static
    {
        return $this->with(['user' => $user, 'pass' => $password]);
    }

    /**
     * Return an instance with the specified host.
     *
     * @param   string  $host  The URI host.
     *
     * @return  static  A new instance with the specified host.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withHost(string $host): static
    {
        return $this->with(['host' => $host]);
    }

    /**
     * Return an instance with the specified port.
     *
     * @param   ?int  $port  The URI port, null to remove it.
     *
     * @return  static  A new instance with the specified port.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withPort(?int $port): static
    {
        return $this->with(['port' => $port]);
    }

    /**
     * Return an instance with the specified path.
     *
     * Like Uri::setPath(), `.` and `..` segments and repeated slashes are resolved.
     *
     * @param   string  $path  The URI path.
     *
     * @return  static  A new instance with the specified path.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withPath(string $path): static
    {
        return $this->with(['path' => $this->cleanPath($path)]);
    }

    /**
     * Return an instance with the specified query.
     *
     * The query can be given as an already encoded string (foo=bar&x=y), which is used as it is, or as an array of
     * variables, from which the query string is built like after Uri::setQuery().
     *
     * @param   array|string  $query  The query string or array.
     *
     * @return  static  A new instance with the specified query.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withQuery(array|string $query): static
    {
        if (\is_array($query)) {
            return $this->with(['vars' => $query, 'query' => null]);
        }

        $query = str_replace('&amp;', '&', $query);

        parse_str($query, $vars);

        return $this->with(['vars' => $vars, 'query' => $query]);
    }

    /**
     * Return an instance with the specified fragment.
     *
     * @param   string  $fragment  The URI fragment.
     *
     * @return  static  A new instance with the specified fragment.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function withFragment(string $fragment): static
    {
        return $this->with(['fragment' => $fragment]);
    }

    /**
     * Return a copy of this instance with the given properties changed.
     *
     * @param   array  $properties  The property names and their new values.
     *
     * @return  static
     *
     * @since   __DEPLOY_VERSION__
     */
    private function with(array $properties): static
    {
        $clone = clone $this;

        foreach ($properties as $name => $value) {
            $clone->$name = $value;
        }

        return $clone;
    }
}
