<?php

namespace Joomla\Uri\Exception;

class InvalidUrlException extends UrlParserException
{
	public function __construct(\Exception $previous = null)
	{
		parent::__construct(
			'URL could not be parsed',
			$previous
		);
	}
}
