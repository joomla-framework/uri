<?php

namespace Joomla\Uri\Exception;

class UnsupportedSchemeException extends UrlParserException
{
	public function __construct($scheme, \Exception $previous = null)
	{
		parent::__construct(
			"Scheme $scheme is not supported",
			$previous
		);
	}
}
