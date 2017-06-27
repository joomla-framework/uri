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
	 * @since   1.0
	 */
	public static function parse_url($url, $component = -1)
	{
		// If no UTF-8 chars in the url just parse it using php native parse_url which is faster.
		if (utf8_decode($url) === $url)
		{
			return parse_url($url, $component);
		}

		// If mbstring extension is loaded force mb conversion to UTF-8.
		if (extension_loaded('mbstring') === true && function_exists('mb_convert_encoding') === true)
		{
			return parse_url(mb_convert_encoding($url, 'UTF-8'), $component);
		}

		// Fallback to the old slower custom method to encode utf-8 chars before parsing the url.

		// Build arrays of values we need to decode before parsing
		$entities     = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D'];
		$replacements = ['!', '*', '\'', '(', ')', ';', ':', '@', '&', '=', '$', ',', '/', '?', '#', '[', ']'];

		// Parse the encoded URL with special URL characters decoded so it can be parsed
		$parts = parse_url(str_replace($entities, $replacements, urlencode($url)), $component);

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
