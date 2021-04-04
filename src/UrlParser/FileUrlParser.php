<?php

namespace Joomla\Uri\UrlParser;

use Joomla\Uri\UrlParser;
use Joomla\Utilities\RegEx;

/**
 * Class FileUrlParser
 *
 * @since __DEPLOY_VERSION__
 */
class FileUrlParser extends UrlParser
{
	/**
	 * A file URL takes the form:
	 *
	 * file://<host>/<path>
	 *
	 * where <host> is the fully qualified domain name of the system on
	 * which the <path> is accessible, and <path> is a hierarchical
	 * directory path of the form <directory>/<directory>/.../<name>.
	 *
	 * For example, a VMS file
	 *
	 * DISK$USER:[MY.NOTES]NOTE123456.TXT
	 *
	 * might become
	 *
	 * <URL:file://vms.host.edu/disk$user/my/notes/note12345.txt>
	 *
	 * As a special case, <host> can be the string "localhost" or the empty
	 * string; this is interpreted as `the machine from which the URL is
	 * being interpreted'.
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

		$parts = parent::parse($url, $options);

		$component = '[^/]+';
		$dir       = RegEx::oneOrMore(RegEx::optional($component) . '/');
		$name      = RegEx::capture($component, 'name');
		$path      = RegEx::capture(RegEx::optional($dir) . $name, 'path');

		$pattern = '/' . $path;

		$pattern = "~^$pattern$~";

		$parts2 = RegEx::match($pattern, $parts['urlpath']);

		$this->urlDecode($parts, 'path');
		$this->urlDecode($parts, 'name');

		return array_merge($parts, $parts2);
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

			if (!$this->isEncoded($parts['path']))
			{
				$path = explode('/', $parts['path']);

				foreach ($path as $key => $value)
				{
					$value  = urlencode($value);
					$oath[$key] = $value;
				}

				$parts['path'] = implode('/', $path);
			}

			$url .= '/';
			$url .= $parts['path'];
		}

		return $url;
	}
}
