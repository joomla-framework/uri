<?php

namespace Joomla\Uri\Exception;

class LoginNotAllowedException extends UrlParserException
{
	public function __construct(\Exception $previous = null)
	{
		parent::__construct(
			'User name or password is not allowed',
			$previous
		);
	}
}
