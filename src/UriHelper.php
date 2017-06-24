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
	 * @param   string $url URL to parse
	 *
	 * @return  mixed  Associative array or false if badly formed URL.
	 *
	 * @link    https://secure.php.net/manual/en/function.parse-url.php
	 * @since   1.0
	 */
	public static function parse_url($url)
	{
		// Get the current LC_CTYPE.
		$currentLcCType = @setlocale(LC_CTYPE, '0');

		// For utf-8 LC_CTYPE just use PHP native method.
		if (stripos($currentLcCType, 'utf-8') !== false)
		{
			return parse_url($url);
		}

		// For non utf-8 LC_CTYPE change the LC_CTYPE locale before parsing.
		@setlocale(LC_CTYPE, 'C');
		$parsedUrl = parse_url($url);
		@setlocale(LC_CTYPE, $currentLcCType);

		return $parsedUrl;
	}
}
