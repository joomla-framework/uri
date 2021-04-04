<?php

namespace Joomla\Uri\Exception;

/**
 * Class UrlParserException
 *
 * @since __DEPLOY_VERSION__
 */
class UrlParserException extends \InvalidArgumentException
{
	public function __construct($message, \Exception $previous = null)
	{
		parent::__construct(
			$message,
			0,
			$previous
		);
	}
}
