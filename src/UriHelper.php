<?php
/**
 * Part of the Joomla Framework Uri Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri;

/**
 * Uri Helper
 *
 * This class provides a UTF-8 safe version of parse_url().
 *
 * @since  1.0
 */
class UriHelper
{
	/**
	 * Array indexed by valid scheme names to their corresponding ports.
	 *
	 * @var    int[]
	 * @since  __DELPOY_VERSION__
	 */
	protected static $allowedSchemes = array(
		'http'  => 80,
		'https' => 443,
	);

	/**
	 * Does a UTF-8 safe version of PHP parse_url function
	 *
	 * @param   string   $url        URL to parse
	 * @param   integer  $component  Retrieve just a specific URL component
	 *
	 * @return  mixed  Associative array or false if badly formed URL.
	 *
	 * @link    https://secure.php.net/manual/en/function.parse-url.php
	 * @since   1.0
	 */
	public static function parse_url($url, $component = -1)
	{
		// If no UTF-8 chars in the url just parse it using php native parse_url which is faster.
		if (utf8_decode($url) === $url)
		{
			return parse_url($url, $component);
		}

		// URL with UTF-8 chars in the url.

		// Build the reserved uri encoded characters map.
		$reservedUriCharactersMap = [
			'%21' => '!',
			'%2A' => '*',
			'%27' => '\'',
			'%28' => '(',
			'%29' => ')',
			'%3B' => ';',
			'%3A' => ':',
			'%40' => '@',
			'%26' => '&',
			'%3D' => '=',
			'%24' => '$',
			'%2C' => ',',
			'%2F' => '/',
			'%3F' => '?',
			'%23' => '#',
			'%5B' => '[',
			'%5D' => ']',
		];

		// Encode the URL (so UTF-8 chars are encoded), revert the encoding in the reserved uri characters and parse the url.
		$parts = parse_url(strtr(urlencode($url), $reservedUriCharactersMap), $component);

		// With a well formed url decode the url (so UTF-8 chars are decoded).
		return $parts ? array_map('urldecode', $parts) : $parts;
	}

	/**
	 * Is the scheme supported by this implementation
	 *
	 * @param   string  $scheme  The current scheme
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function isSupportedScheme($scheme)
	{
		return array_key_exists(strtolower($scheme), static::$allowedSchemes);
	}

	/**
	 * Is a given port non-standard for the current scheme?
	 *
	 * @param   string   $scheme  The current scheme
	 * @param   integer  $port    The port for the current scheme
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function isNonStandardPort($scheme, $port)
	{
		// If no scheme is present then return false
		if (empty($scheme))
		{
			return true;
		}

		if (!$port)
		{
			return false;
		}

		return !static::isSupportedScheme($scheme) || $port !== static::$allowedSchemes[$scheme];
	}
}
