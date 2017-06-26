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
	 * Does a UTF-8 safe version of PHP parse_url function
	 *
	 * @param   string   $url        URL to parse
	 * @param   integer  $component  Retrieve just a specific URL component
	 *
	 * @return  mixed  Associative array or false if badly formed URL.
	 *
	 * @link    https://secure.php.net/manual/en/function.parse-url.php
	 * @link    https://secure.php.net/manual/en/function.setlocale.php
	 * @since   1.0
	 */
	public static function parse_url($url, $component = -1)
	{
		// If no UTF-8 chars in the url just parse it using php native parse_url which is faster.
		if (preg_match('#^.#u', $url) === 0)
		{
			return parse_url($url, $component);
		}

		// Get the current LC_CTYPE locale.
		$currentLocaleLcCType = @setlocale(LC_CTYPE, 0);

		// If UTF-8 locale found, just use PHP native method which is faster.
		if ($currentLocaleLcCType !== false
			&& ($currentLocaleLcCType === 'C' || stripos($currentLocaleLcCType, 'UTF-8') !== false || stripos($currentLocaleLcCType, 'UTF8') !== false))
		{
			return parse_url($url, $component);
		}

		// Non UTF-8 LC_CTYPE locale, try to use 'C' locale for parsing the url.
		if ($currentLocaleLcCType !== false && @setlocale(LC_CTYPE, 'C') === 'C')
		{
			$parsedUrl = parse_url($url, $component);

			// Go back to previous locale.
			@setlocale(LC_CTYPE, $currentLocaleLcCType);

			return $parsedUrl;
		}

		// Fallback to the old slower custom method to encode utf-8 chars before parsing the url.

		// Build arrays of values we need to decode before parsing
		$entities     = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D'];
		$replacements = ['!', '*', '\'', '(', ')', ';', ':', '@', '&', '=', '$', ',', '/', '?', '#', '[', ']'];

		// Create encoded URL with special URL characters decoded so it can be parsed
		// All other characters will be encoded
		$encodedURL = str_replace($entities, $replacements, urlencode($url));

		// Parse the encoded URL
		$parts = parse_url($encodedURL, $component);

		// Now, decode each value of the resulting array
		if ($parts)
		{
			foreach ($parts as &$value)
			{
				$value = urldecode(str_replace($replacements, $entities, $value));
			}
		}

		return $parts;
	}
}
