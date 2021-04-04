<?php

namespace Joomla\Uri\Exception;

class MissingHostException extends UrlParserException
{
	public function __construct(\Exception $previous = null)
	{
		parent::__construct(
			'URL does not specify a host',
			$previous
		);
	}

}
