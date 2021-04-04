<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\Exception\InvalidCharacterException;
use Joomla\Uri\Exception\InvalidUrlException;
use Joomla\Uri\Exception\LoginNotAllowedException;
use Joomla\Uri\Exception\MissingHostException;
use Joomla\Uri\Exception\UnsupportedSchemeException;
use Joomla\Uri\Url;
use Joomla\Uri\UrlParser\HttpUrlParser;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\Uri class.
 *
 * @since  __DEPLOY_VERSION__
 */
class UrlParserTest extends TestCase
{
	public function sampleUrls()
	{
		// Input URL, isValid?, sanitised URL
		return array(
			array(
				'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
				false,
				'http://www.example.com:80/path/file.html?var=value#fragment'
			),
			array(
				'http://www.example.com:80/administrator/',
				true,
				'http://www.example.com:80/administrator/'
			),
			array(
				'http://someuser:somepass@www.example.com:80/path/file.html?var=value&test=true#fragment',
				false,
				'http://www.example.com:80/path/file.html?var=value&test=true#fragment'
			),
			array(
				'http://www.example.com/path/file.html?field[price][from]=5&field[price][to]=10&field[name]=foo&v=45',
				true,
				'http://www.example.com/path/file.html?field[price][from]=5&field[price][to]=10&field[name]=foo&v=45'
			),
			array(
				'http://ümläütdömain.de',
				false,
				'http://xn--mltdmain-1za6p8bd.de'
			),
			array(
				'http://xn--mltdmain-1za6p8bd.de',
				true,
				'http://xn--mltdmain-1za6p8bd.de'
			),
			array(
				'http://ümläütdömain.de/index.php/test-ärticle?query=föø&öther=bær#änchör',
				false,
				'http://xn--mltdmain-1za6p8bd.de/index.php/test-%C3%A4rticle?query=f%C3%B6%C3%B8&%C3%B6ther=b%C3%A6r#%C3%A4nch%C3%B6r'
			),
			array(
				'http://localhost:8080',
				true,
				'http://localhost:8080'
			),
			array(
				'http://localhost:8080/index.php/test-article',
				true,
				'http://localhost:8080/index.php/test-article'
			),
			array(
				'http://www.example.com',
				true,
				'http://www.example.com'
			),
			array(
				'http://127.0.0.1/index.php/test-article',
				true,
				'http://127.0.0.1/index.php/test-article'
			),
			array(
				'http://www.sec.example.com?query=foo&other=bar#anchor',
				true,
				'http://www.sec.example.com?query=foo&other=bar#anchor'
			),
			array(
				'http://johndoe:secret@localhost:8080/',
				false,
				'http://localhost:8080'
			),
			array(
				'http://johndoe:secret@www.example.com/',
				false,
				'http://www.example.com'
			),
			array(
				'http://www.example.com\localhost:8080/',
				true,
				'http://www.example.com/localhost:8080/'
			),
			array(
				'http://www.example.com\@localhost:8080/',
				true,
				'http://www.example.com/@localhost:8080/'
			),
			array(
				'www.example.com',
				true,
				'http://www.example.com'
			),
			array(
				'file://vms.host.edu/disk$user/my/notes/note12345.txt',
				true,
				'file://vms.host.edu/disk$user/my/notes/note12345.txt'
			),
			array(
				'file://johndoe:secret@vms.host.edu/disk$user/my/notes/note12345.txt',
				true,
				'file://johndoe:secret@vms.host.edu/disk$user/my/notes/note12345.txt'
			),
			array(
				'file://johndoe:sec%3Aret%3F@vms.host.edu/disk$user/my/notes/note12345.txt',
				true,
				'file://johndoe:sec%3Aret%3F@vms.host.edu/disk$user/my/notes/note12345.txt'
			),
		);
	}

	/**
	 * @param   string   $url
	 * @param   boolean  $expectedValidationResult
	 * @param   string   $sanitisedUrl
	 *
	 * @dataProvider sampleUrls
	 */
	public function testValidation($url, $expectedValidationResult, $sanitisedUrl)
	{
		$this->assertEquals($expectedValidationResult, Url::isValid($url));
		$this->assertEquals($sanitisedUrl, Url::sanitise($url));
	}

	/**
	 * An UnsupportedSchemeException is thrown, if the protocol is not supported by the parser
	 */
	public function testUnsupportedScheme()
	{
		try
		{
			Url::sanitise('ftp://example.com');
			$this->fail('Expected exception not thrown');
		}
		catch (UnsupportedSchemeException $exception)
		{
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * @testdox An InvalidCharacterException is thrown, if the URL contains invalid characters
	 */
	public function testInvalidCharacters()
	{
		try
		{
			$parser = new HttpUrlParser();
			$parser->parse('This is not a URL');
			$this->fail('Expected exception not thrown');
		}
		catch (InvalidCharacterException $exception)
		{
			$this->addToAssertionCount(1);
		}

	}

	/**
	 * @testdox An InvalidUrlException is thrown, if URL cannot be parsed
	 */
	public function testInvalidUrl()
	{
		try
		{
			$parser = new HttpUrlParser();
			$parser->parse('123');
			$this->fail('Expected exception not thrown');
		}
		catch (InvalidUrlException $exception)
		{
			$this->addToAssertionCount(1);
		}

	}

	/**
	 * @testdox A MissingHostException is thrown, if no host is specified
	 */
	public function testMissingHost()
	{
		try
		{
			$parser = new HttpUrlParser();
			$parser->parse('http:///index.php');
			$this->fail('Expected exception not thrown');
		}
		catch (MissingHostException $exception)
		{
			$this->addToAssertionCount(1);
		}

	}

	/**
	 * @testdox When fixing is allowed, host is set to localhost, if no host is specified
	 */
	public function testFixMissingHost()
	{
		$parser = new HttpUrlParser();
		$result = $parser->parse('http:///index.php', array('fix' => true));

		$this->assertEquals('localhost', $result['host']);
	}

	/**
	 * @testdox A LoginNotAllowedException is thrown, if 'user[:pass]@' is specified
	 */
	public function testLogin1()
	{
		$url = 'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment';

		try
		{
			Url::build(Url::parse($url));
			$this->fail('Expected exception not thrown');
		} catch (LoginNotAllowedException $exception) {
			$this->addToAssertionCount(1);
		}
	}

	/**
	 * @testdox User and password are removed, if option 'fix' is true
	 */
	public function testLogin2()
	{
		$url = 'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment';
		$expected = 'http://www.example.com:80/path/file.html?var=value#fragment';

		$sanitisedUrl = Url::build(Url::parse($url, array('fix' => true)));
		$this->assertEquals($expected, $sanitisedUrl);
	}

	/**
	 * @testdox User and password are kept, if option 'allowLogin' is true
	 */
	public function testLogin3()
	{
		$url = 'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment';

		$sanitisedUrl = Url::build(Url::parse($url, array('allowLogin' => true)));
		$this->assertEquals($url, $sanitisedUrl);
	}

	/**
	 * @testdox User and password are escaped, if option 'fix' is true
	 */
	public function testLogin4()
	{
		$url = 'http://someüser:somepäss@www.example.com:80/path/file.html?var=value#fragment';
		$expected = 'http://some%C3%BCser:somep%C3%A4ss@www.example.com/path/file.html?var=value#fragment';

		$sanitisedUrl = Url::build(Url::parse($url, array('fix' => true, 'allowLogin' => true)));
		$this->assertEquals($expected, $sanitisedUrl);
	}

	/**
	 * @testdox Default port is added, if option 'addPort' is true
	 */
	public function testAddPort()
	{
		$url = 'http://www.example.com/path/file.html?var=value#fragment';
		$expected = 'http://www.example.com:80/path/file.html?var=value#fragment';

		$sanitisedUrl = Url::build(Url::parse($url, array('addPort' => true)));
		$this->assertEquals($expected, $sanitisedUrl);

		$url = 'https://www.example.com/path/file.html?var=value#fragment';
		$expected = 'https://www.example.com:443/path/file.html?var=value#fragment';

		$sanitisedUrl = Url::build(Url::parse($url, array('addPort' => true)));
		$this->assertEquals($expected, $sanitisedUrl);
	}

	public function sampleParts()
	{
		/** parts array, expected url */
		return array(
			array(
				array(
					'scheme' => 'http',
					'host'   => 'localhost',
					'path'   => '/absolute/path/index.php',
					'query'  => 'foo=1&bar=2',
				),
				'http://localhost/%2Fabsolute/path/index.php?foo=1&bar=2'
			),
			array(
				array(
					'host'   => 'localhost',
					'path'   => 'path/index.php',
				),
				'http://localhost/path/index.php'
			),
			array(
				array(
					'scheme' => 'http',
					'host'   => 'xn--mltdmain-1za6p8bd.de',
					'path'   => 'path/index.php',
				),
				'http://xn--mltdmain-1za6p8bd.de/path/index.php'
			),
			array(
				array(
					'scheme' => 'file',
					'user' => 'johndoe',
					'pass' => 'secret',
					'path'   => '/absolute/path/index.php',
				),
				'file://johndoe:secret@/%2Fabsolute/path/index.php'
			),
			array(
				array(
					'scheme' => 'file',
					'user' => 'johndoe',
					'pass' => 'secret',
					'path'   => '%2Fabsolute/path/index.php',
				),
				'file://johndoe:secret@/%2Fabsolute/path/index.php'
			),
		);
	}

	/**
	 * @dataProvider sampleParts
	 */
	public function testBuild($parts, $expectedUrl)
	{
		$url = Url::build($parts);

		$this->assertEquals($expectedUrl, $url);
	}
}
