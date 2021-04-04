<?php
/**
 * Part of the Joomla Framework Uri Package
 *
 * @copyright  Copyright (C) 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri;

use Joomla\Uri\Exception\UnsupportedSchemeException;
use Joomla\Uri\Exception\UrlParserException;

/**
 * Facade for different URL parsers
 *
 * Automatically selects the required parser depending on the scheme.
 *
 * @since __DEPLOY_VERSION__
 */
abstract class Url
{
	const DEFAULT_SCHEME = 'http';

	/** @var string[] Supported schemes */
	private static $classMap = array(
		'http'   => '\\Joomla\\Uri\\UrlParser\\HttpUrlParser',
		'https'  => '\\Joomla\\Uri\\UrlParser\\HttpUrlParser',
		'file'   => '\\Joomla\\Uri\\UrlParser\\FileUrlParser',
	);

	/**
	 * @param   string  $url  URL to be sanitised
	 *
	 * @return string  The sanitised URL
	 */
	public static function sanitise($url)
	{
		return self::build(self::parse($url, array('fix' => true)));
	}

	/**
	 * @param   string  $url  URL to be validated
	 *
	 * @return  boolean  Whether the URL is valid
	 */
	public static function isValid($url)
	{
		try
		{
			self::parse($url, array('fix' => false));

			return true;
		}
		catch (UrlParserException $exception)
		{
			return false;
		}
	}

	/**
	 * @param   string  $url      The URL to parse
	 * @param   array   $options  The options
	 *                            addPort: boolean  Always add the port if true
	 *                            fix: boolean  Try to fix the URL
	 *
	 * @return  string[]
	 */
	public static function parse($url, $options = array())
	{
		return self::getParserFor($url)->parse($url, $options);
	}

	/**
	 * @param   string[]  $parts  The URL components
	 *
	 * @return  string  The URL
	 */
	public static function build(array $parts)
	{
		if (!isset($parts['scheme']))
		{
			$parts['scheme'] = self::DEFAULT_SCHEME;
		}

		return self::getParserFor($parts['scheme'])->build($parts);
	}

	/**
	 * @param   string  $url  URL to be parsed
	 *
	 * @return UrlParser
	 */
	private static function getParserFor($url)
	{
		if (preg_match('~^(\w+)(?:$|://)~', $url, $parts))
		{
			$scheme = strtolower($parts[1]);
		}
		else
		{
			$scheme = self::DEFAULT_SCHEME;
		}

		if (!isset(self::$classMap[$scheme]) || !class_exists(self::$classMap[$scheme]))
		{
			throw new UnsupportedSchemeException($scheme);
		}

		return new self::$classMap[$scheme];
	}
}
