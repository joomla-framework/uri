<?php

/**
 * Part of the Joomla Framework Uri Package
 *
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri;

/**
 * Uri Class
 *
 * This class parses a URI and provides a common interface for the Joomla Framework to access and manipulate a URI.
 *
 * @since  1.0
 */
class Uri extends AbstractUri
{
    /**
     * Adds a query variable and value, replacing the value if it already exists and returning the old value
     *
     * @param   string  $name   Name of the query variable to set.
     * @param   mixed   $value  Value of the query variable.
     *
     * @return  mixed   Previous value for the query variable.
     *
     * @since   1.0
     */
    public function setVar(string $name, mixed $value): mixed
    {
        $tmp = $this->vars[$name] ?? null;

        $this->vars[$name] = $value;

        // Empty the query
        $this->query = null;

        return $tmp;
    }

    /**
     * Removes an item from the query string variables if it exists
     *
     * @param   string  $name  Name of variable to remove.
     *
     * @return  void
     *
     * @since   1.0
     */
    public function delVar(string $name): void
    {
        if (array_key_exists($name, $this->vars)) {
            unset($this->vars[$name]);

            // Empty the query
            $this->query = null;
        }
    }

    /**
     * Sets the query to a supplied string in format foo=bar&x=y
     *
     * @param   array|string  $query  The query string or array.
     *
     * @return  void
     *
     * @since   1.0
     */
    public function setQuery(array|string $query): void
    {
        if (\is_array($query)) {
            $this->vars = $query;
        } else {
            if (strpos($query, '&amp;') !== false) {
                $query = str_replace('&amp;', '&', $query);
            }

            parse_str($query, $this->vars);
        }

        // Empty the query
        $this->query = null;
    }

    /**
     * Set the URI scheme (protocol)
     *
     * @param   ?string  $scheme  The URI scheme.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setScheme(?string $scheme): static
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Set the URI username
     *
     * @param   ?string  $user  The URI username.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setUser(?string $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the URI password
     *
     * @param   ?string  $pass  The URI password.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setPass(?string $pass): static
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Set the URI host
     *
     * @param   ?string  $host  The URI host.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setHost(?string $host): static
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Set the URI port
     *
     * @param   ?integer  $port  The URI port number.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setPort(?int $port): static
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Set the URI path string
     *
     * @param   string  $path  The URI path string.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setPath(string $path): static
    {
        $this->path = $this->cleanPath($path);

        return $this;
    }

    /**
     * Set the URI anchor string
     *
     * @param   ?string  $anchor  The URI anchor string.
     *
     * @return  static  This method supports chaining.
     *
     * @since   1.0
     */
    public function setFragment(?string $anchor): static
    {
        $this->fragment = $anchor;

        return $this;
    }
}
