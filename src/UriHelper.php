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
	 * @param   string $url URL to parse
	 *
	 * @return  mixed  Associative array or false if badly formed URL.
	 *
	 * @see     http://us3.php.net/manual/en/function.parse-url.php
	 * @since   1.0
	 */
	public static function parse_url($url)
	{
		$result = false;

		// Build arrays of values we need to decode before parsing
		$entities     = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D'];
		$replacements = ['!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "$", ",", "/", "?", "#", "[", "]"];

		// Create encoded URL with special URL characters decoded so it can be parsed
		// All other characters will be encoded
		$encodedURL = str_replace($entities, $replacements, urlencode($url));

		// Parse the encoded URL
		$encodedParts = parse_url($encodedURL);

		// Now, decode each value of the resulting array
		if ($encodedParts)
		{
			$result = [];

			foreach ($encodedParts as $key => $value)
			{
				$result[$key] = urldecode(str_replace($replacements, $entities, $value));
			}
		}

		return $result;
	}

	/**
	 * Is the scheme supported by this implementation
	 *
	 * @param   string  $scheme  The current scheme
	 *
	 * @return  bool
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
	 * @param   string  $scheme  The current scheme
	 * @param   int     $port    The port for the current scheme
	 *
	 * @return  bool
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
