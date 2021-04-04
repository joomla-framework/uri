<?php

namespace Joomla\Uri;

use Algo26\IdnaConvert\Exception\AlreadyPunycodeException;
use Algo26\IdnaConvert\ToIdn;
use Algo26\IdnaConvert\ToUnicode;
use Joomla\Uri\Exception\InvalidCharacterException;
use Joomla\Uri\Exception\InvalidUrlException;
use Joomla\Uri\Exception\MissingHostException;
use Joomla\Utilities\RegEx;

/**
 * Class UrlParser
 *
 * @since __DEPLOY_VERSION__
 */
abstract class UrlParser
{
	const ALLOWED_CHARACTERS = 'a-z0-9\-\._\~:/\?#[\]@!\$&\'\(\)\*\+,;=';

	/**
	 * Parse the parts common to all IP URLs.
	 *
	 * The value returned as 'urlpath' needs further parsing according to the scheme by the child class.
	 *
	 * @param   string  $url      The URL to parse
	 * @param   array   $options  The options
	 *                            addPort: boolean  Always add the port if true
	 *                            fix: boolean  Try to fix the URL
	 *
	 * @return  string[]
	 */
	public function parse($url, $options = array())
	{
		$defaultOptions = array(
			'addPort' => false,
			'fix'     => false,
		);

		$options = array_merge($defaultOptions, $options);

		/**
		 * Browsers convert backslashes to slashes on the fly, so it should be save to do the same here
		 */
		$url = str_replace('\\', '/', $url);

		/**
		 * Check for illegal characters according to RFC 3986 Section 2 (complete list of allowed characters in a URI)
		 */
		if ($this->hasInvalidCharacters($url))
		{
			if (!$options['fix'])
			{
				throw new InvalidCharacterException;
			}

			$url = $this->sanitise($url);
		}

		/**
		 * RFC 1738 Section 2.1
		 * Scheme names consist of a sequence of characters. The lower case
		 * letters "a"--"z", digits, and the characters plus ("+"), period
		 * ("."), and hyphen ("-") are allowed. For resiliency, programs
		 * interpreting URLs should treat upper case letters as equivalent to
		 * lower case in scheme names (e.g., allow "HTTP" as well as "http").
		 */
		$scheme = RegEx::capture("[a-zA-Z0-9+.-]+", 'scheme');

		/**
		 * The user name (and password), if present, are followed by a
		 * commercial at-sign "@". Within the user and password field, any ":",
		 * "@", or "/" must be encoded.
		 */
		$user = RegEx::capture('[^:/@]+', 'user');
		$pass = RegEx::capture('[^:/@]+', 'pass');

		/**
		 * The fully qualified domain name of a network host, or its IP
		 * address as a set of four decimal digit groups separated by
		 * ".". Fully qualified domain names take the form as described
		 * in Section 3.5 of RFC 1034 [13] and Section 2.1 of RFC 1123
		 * [5]: a sequence of domain labels separated by ".", each domain
		 * label starting and ending with an alphanumerical character and
		 * possibly also containing "-" characters. The rightmost domain
		 * label will never start with a digit, though, which
		 * syntactically distinguishes all domain names from the IP
		 * addresses.
		 */
		$ip          = RegEx::capture('\d+\.\d+\.\d+\.\d+', 'ip');
		$domainLabel = '[a-z0-9]+' . RegEx::optional('[a-z0-9-]*[a-z0-9]');
		$tld         = RegEx::capture('[a-z]+' . RegEx::optional('[a-z0-9-]*[a-z0-9]'), 'tld');
		$domain      = RegEx::capture($domainLabel . '\.' . $tld, 'domain');
		$subdomain   = RegEx::capture(RegEx::noneOrMore($domainLabel . '\.') . $domainLabel, 'subdomain');
		$host        = RegEx::capture(
			RegEx::optional(
				RegEx::anyOf(
					array(
						$ip,
						RegEx::optional($subdomain . '\.') . RegEx::anyOf(array("localhost", $domain))
					)
				)
			),
			'host'
		);

		/**
		 * The port number to connect to. Most schemes designate
		 * protocols that have a default port number. Another port number
		 * may optionally be supplied, in decimal, separated from the
		 * host by a colon. If the port is omitted, the colon is as well.
		 */
		$port = RegEx::capture('\d+', 'port');

		$pattern = RegEx::optional($scheme . '://');
		$pattern .= RegEx::optional($user . RegEx::optional(':' . $pass) . '@');
		$pattern .= RegEx::optional($host . RegEx::optional(':' . $port));
		$pattern .= RegEx::capture('.*', 'urlpath');
		$pattern = "~^$pattern$~i";

		$parts = RegEx::match($pattern, $url);

		if (empty($parts['urlpath']))
		{
			$parts['urlpath'] = '';
		}

		if ($parts['urlpath'] === $url)
		{
			throw new InvalidUrlException;
		}

		if (empty($parts['host']) && $parts['scheme'] !== 'file')
		{
			if (!$options['fix'])
			{
				throw new MissingHostException;
			}

			$parts['host'] = 'localhost';
		}

		$this->urlDecode($parts, 'user');
		$this->urlDecode($parts, 'pass');

		$toUnicode     = new ToUnicode();
		$parts['host'] = $toUnicode->convert($parts['host']);

		if (!empty($parts['domain']))
		{
			$parts['domain'] = $toUnicode->convert($parts['domain']);
		}

		if (!empty($parts['subdomain']))
		{
			$parts['subdomain'] = $toUnicode->convert($parts['subdomain']);
		}

		return $parts;
	}

	/**
	 * Build the part common to all IP URLs.
	 *
	 * Components making up the urlpath need to be added by child class.
	 *
	 * @param   string[]  $parts  The URL components
	 *
	 * @return  string  The URL
	 */
	public function build(array $parts)
	{
		$url = '';

		if (isset($parts['scheme']))
		{
			$url .= strtolower($parts['scheme']) . '://';
		}

		if (isset($parts['user']))
		{
			$this->urlEncode($parts, 'user');
			$url .= $parts['user'];

			if (isset($parts['pass']))
			{
				$this->urlEncode($parts, 'pass');
				$url .= ':' . $parts['pass'];
			}

			$url .= '@';
		}

		if (isset($parts['host']))
		{
			if ($this->hasInvalidCharacters($parts['host']))
			{
				$parts['host'] = $this->encodeHost($parts['host']);
			}

			$url .= $parts['host'];

			if (isset($parts['port']))
			{
				$url .= ':' . $parts['port'];
			}
		}

		return $url;
	}

	/**
	 * Try to sanitise the URL using Punycode and escaping
	 *
	 * @param   string  $url  The URL to be sanitised
	 *
	 * @return  string
	 */
	private function sanitise($url)
	{
		$parts = parse_url($url);

		// Try to fix unicode domain
		if (!empty($parts['host']))
		{
			$parts['host'] = $this->encodeHost($parts['host']);
		}

		// Encode forbidden characters
		/** @noinspection RegExpDuplicateCharacterInClass */
		$parts = preg_replace_callback(
			'~[^' . self::ALLOWED_CHARACTERS . '%]+~ui',
			static function ($match) {
				return urlencode($match[0]);
			},
			$parts
		);

		$url = $this->buildUrl($parts);

		return $url;
	}

	protected function urlEncode(array &$parts, $index)
	{
		if (isset($parts[$index]))
		{
			$parts[$index] = urlencode($parts[$index]);
		}
	}

	protected function urlDecode(array &$parts, $index)
	{
		if (isset($parts[$index]) && $this->isEncoded($parts[$index]))
		{
			$parts[$index] = urldecode($parts[$index]);
		}
	}

	/**
	 * @param   string  $string
	 *
	 * @return bool
	 */
	protected function hasInvalidCharacters($string)
	{
		return !preg_match(
			'~^' . RegEx::oneOrMore(RegEx::anyOf(array('[' . self::ALLOWED_CHARACTERS . ']', '%[0-9a-f]{2}'))) . '$~i',
			$string
		);
	}

	protected function isEncoded($string)
	{
		return preg_match('~%[0-9a-f]{2}~i', $string) && !$this->hasInvalidCharacters($string);
	}

	/**
	 * Build a URL from PHP's parse_url() result
	 *
	 * @param   array  $parts
	 *
	 * @return  string;
	 */
	private function buildUrl(array $parts)
	{
		$url = empty($parts['scheme']) ? Url::DEFAULT_SCHEME : $parts['scheme'];
		$url .= '://';

		if (isset($parts['user']))
		{
			$url .= $parts['user'];

			if (isset($parts['pass']))
			{
				$url .= ':' . $parts['pass'];
			}

			$url .= '@';
		}

		$url .= empty($parts['host']) ? 'localhost' : $parts['host'];

		if (!empty($parts['path']))
		{
			$url .= $parts['path'];
		}

		if (!empty($parts['query']))
		{
			$url .= '?' . $parts['query'];
		}

		if (!empty($parts['fragment']))
		{
			$url .= '#' . $parts['fragment'];
		}

		return $url;
	}

	/**
	 * @param $host
	 *
	 * @return string
	 */
	private function encodeHost($host)
	{
		try
		{
			$toIdn = new ToIdn;
			$host  = $toIdn->convert($host);
		}
		catch (AlreadyPunycodeException $exception)
		{
			// Ok, do nothing
		}

		if ($host === false)
		{
			// Can't fix
			throw new InvalidCharacterException;
		}

		return $host;
	}
}
