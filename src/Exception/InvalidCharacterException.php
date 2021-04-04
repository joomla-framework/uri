<?php

namespace Joomla\Uri\Exception;

/**
 * Class InvalidCharacterException
 *
 * @since __DEPLOY_VERSION__
 */
class InvalidCharacterException extends UrlParserException
{
	public function __construct(\Exception $previous = null)
	{
		parent::__construct(
			'URL contains invalid character(s)',
			$previous
		);
	}
}
