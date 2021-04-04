<?php

namespace Joomla\Uri\UrlParser;

use Joomla\Uri\Exception\LoginNotAllowedException;
use Joomla\Uri\UrlParser;
use Joomla\Utilities\RegEx;

/**
 * Class HttpUrlParser
 *
 * @since __DEPLOY_VERSION__
 */
class HttpUrlParser extends UrlParser
{
	/**
	 * An HTTP URL takes the form:
	 *
	 * http://<host>:<port>/<path>?<searchpart>
	 *
	 * No user name or password is allowed.
	 * <path> is an HTTP selector, and <searchpart> is a query
	 * string. The <path> is optional, as is the <searchpart> and its
	 * preceding "?". If neither <path> nor <searchpart> is present, the "/"
	 * may also be omitted.
	 *
	 * Within the <path> and <searchpart> components, "/", ";", "?" are
	 * reserved.  The "/" character may be used within HTTP to designate a
	 * hierarchical structure.
	 *
	 * @param   string  $url      The URL to parse
	 * @param   array   $options  The options
	 *                            addPort: boolean  Always add the port if true
	 *                            fix: boolean  Try to fix the URL
	 *                            allowLogin: boolean  Allow user:pass@ although forbidden
	 *
	 * @return  string[]
	 */
	public function parse($url, $options = array())
	{
		$defaultOptions = array(
			'addPort'    => false,
			'fix'        => false,
			'allowLogin' => false,
		);

		$options = array_merge($defaultOptions, $options);

		$parts = parent::parse($url, $options);

		if (!$options['allowLogin'] && !empty($parts['user']))
		{
			if (!$options['fix'])
			{
				throw new LoginNotAllowedException();
			}

			unset($parts['user'], $parts['pass']);
		}

		if ($options['addPort'] && empty($parts['port']))
		{
			$parts['port'] = $parts['scheme'] === 'https' ? 443 : 80;
		}

		$dir      = RegEx::capture('[^?#]*', 'path');
		$query    = RegEx::capture('[^#]+', 'query');
		$fragment = RegEx::capture('.+', 'fragment');

		$pattern = RegEx::optional('/' . $dir)
				   . RegEx::optional('\?' . $query)
				   . RegEx::optional('#' . $fragment);

		$pattern = "~^$pattern$~";

		preg_match($pattern, $parts['urlpath'], $parts2);

		$parts = array_filter(
			array_merge($parts, $parts2),
			static function ($value, $key) {
				return !is_numeric($key) && !empty($value);
			},
			ARRAY_FILTER_USE_BOTH
		);

		if (isset($parts['query']))
		{
			$query = array();
			parse_str($parts['query'], $query);
			$parts['query'] = $query;
		}

		if (isset($parts['path']) && $this->isEncoded($parts['path']))
		{
			$parts['path'] = urldecode($parts['path']);
		}

		if (isset($parts['fragment']) && $this->isEncoded($parts['fragment']))
		{
			$parts['fragment'] = urldecode($parts['fragment']);
		}

		return $parts;
	}

	/**
	 * @param   string[]  $parts  The URL components
	 *
	 * @return  string  The URL
	 */
	public function build(array $parts)
	{
		$url = parent::build($parts);

		if (isset($parts['path']))
		{
			if ($parts['path'][0] === '/')
			{
				$parts['path'] = '%2F' . substr($parts['path'], 1);
			}

			if ($this->hasInvalidCharacters($parts['path']))
			{
				$path = explode('/', $parts['path']);

				foreach (array_keys($path) as $key)
				{
					$this->urlEncode($path, $key);
				}

				$parts['path'] = implode('/', $path);
			}

			$url .= '/' . $parts['path'];
		}

		if (isset($parts['query']))
		{
			if (!is_array($parts['query']))
			{
				$query = array();
				parse_str($parts['query'], $query);
				$parts['query'] = $query;
			}

			$url .= '?' . str_replace(array('%5B', '%5D'), array('[', ']'), http_build_query($parts['query']));
		}

		if (isset($parts['fragment']))
		{
			if ($this->hasInvalidCharacters($parts['fragment']))
			{
				$this->urlEncode($parts, 'fragment');
			}

			$url .= '#' . $parts['fragment'];
		}

		return $url;
	}
}
