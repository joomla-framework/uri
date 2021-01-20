<?php
/**
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\Uri;
use Joomla\Test\TestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\Uri class.
 *
 * @since  1.0
 */
class UriTest extends TestCase
{
	/**
	 * Object under test
	 *
	 * @var    Uri
	 * @since  1.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function setUp()
	{
		$this->object = new Uri('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');
	}

	/**
	 * Test the __toString method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function test__toString()
	{
		$this->assertThat(
			$this->object->__toString(),
			$this->equalTo('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment')
		);
	}

	/**
	 * Test the buildQuery method.
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function testBuildQuery()
	{
		$this->assertThat(
			TestHelper::invoke(
				$this->object,
				'buildQuery',
				array(
					'var' => 'value',
					'foo' => 'bar'
				)
			),
			$this->equalTo('var=value&foo=bar')
		);
	}

	/**
	 * Test the cleanPath method.
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function testcleanPath()
	{
		$this->assertThat(
			TestHelper::invoke(
				$this->object,
				'cleanPath',
				'/foo/bar/../boo.php'
			),
			$this->equalTo('/foo/boo.php')
		);

		$this->assertThat(
			TestHelper::invoke(
				$this->object,
				'cleanPath',
				'/foo/bar/../../boo.php'
			),
			$this->equalTo('/boo.php')
		);

		$this->assertThat(
			TestHelper::invoke(
				$this->object,
				'cleanPath',
				'/foo/bar/.././/boo.php'
			),
			$this->equalTo('/foo/boo.php')
		);
	}

	/**
	 * Test the parse method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testConstruct()
	{
		$object = new Uri('http://someuser:somepass@www.example.com:80/path/file.html?var=value&amp;test=true#fragment');

		$this->assertThat(
			$object->getHost(),
			$this->equalTo('www.example.com')
		);

		$this->assertThat(
			$object->getPath(),
			$this->equalTo('/path/file.html')
		);

		$this->assertThat(
			$object->getScheme(),
			$this->equalTo('http')
		);
	}

	/**
	 * Test the toString method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testToString()
	{
		$this->assertThat(
			$this->object->toString(),
			$this->equalTo('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment')
		);

		$this->object->setQuery('somevar=somevalue');
		$this->object->setVar('somevar2', 'somevalue2');
		$this->object->setScheme('ftp');
		$this->object->setUser('root');
		$this->object->setPass('secret');
		$this->object->setHost('www.example.org');
		$this->object->setPort('8888');
		$this->object->setFragment('someFragment');
		$this->object->setPath('/this/is/a/path/to/a/file');

		$this->assertThat(
			$this->object->toString(),
			$this->equalTo('ftp://root:secret@www.example.org:8888/this/is/a/path/to/a/file?somevar=somevalue&somevar2=somevalue2#someFragment')
		);
	}

	/**
	 * Test the setVar method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetVar()
	{
		$this->object->setVar('somevariable', 'somevalue');

		$this->assertThat(
			$this->object->getVar('somevariable'),
			$this->equalTo('somevalue')
		);
	}

	/**
	 * Test the hasVar method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testHasVar()
	{
		$this->assertThat(
			$this->object->hasVar('somevariable'),
			$this->equalTo(false)
		);

		$this->assertThat(
			$this->object->hasVar('var'),
			$this->equalTo(true)
		);
	}

	/**
	 * Test the getVar method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetVar()
	{
		$this->assertThat(
			$this->object->getVar('var'),
			$this->equalTo('value')
		);

		$this->assertThat(
			$this->object->getVar('var2'),
			$this->equalTo('')
		);

		$this->assertThat(
			$this->object->getVar('var2', 'default'),
			$this->equalTo('default')
		);
	}

	/**
	 * Test the delVar method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testDelVar()
	{
		$this->assertThat(
			$this->object->getVar('var'),
			$this->equalTo('value')
		);

		$this->object->delVar('var');

		$this->assertThat(
			$this->object->getVar('var'),
			$this->equalTo('')
		);
	}

	/**
	 * Test the setQuery method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetQuery()
	{
		$this->object->setQuery('somevar=somevalue');

		$this->assertThat(
			$this->object->getQuery(),
			$this->equalTo('somevar=somevalue')
		);

		$this->object->setQuery('somevar=somevalue&amp;test=true');

		$this->assertThat(
			$this->object->getQuery(),
			$this->equalTo('somevar=somevalue&test=true')
		);

		$this->object->setQuery(array('somevar' => 'somevalue', 'test' => 'true'));

		$this->assertThat(
			$this->object->getQuery(),
			$this->equalTo('somevar=somevalue&test=true')
		);
	}

	/**
	 * Test the getQuery method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetQuery()
	{
		$this->assertThat(
			$this->object->getQuery(),
			$this->equalTo('var=value')
		);

		$this->assertThat(
			$this->object->getQuery(true),
			$this->equalTo(array('var' => 'value'))
		);

		// Set a new query
		$this->object->setQuery('somevar=somevalue');

		// Test if query is null, to build query in getQuery call.
		$this->assertThat(
			$this->object->getQuery(),
			$this->equalTo('somevar=somevalue')
		);
	}

	/**
	 * Test the getScheme method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetScheme()
	{
		$this->assertThat(
			$this->object->getScheme(),
			$this->equalTo('http')
		);
	}

	/**
	 * Test the setScheme method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetScheme()
	{
		$this->object->setScheme('ftp');

		$this->assertThat(
			$this->object->getScheme(),
			$this->equalTo('ftp')
		);
	}

	/**
	 * Test the getUser method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetUser()
	{
		$this->assertThat(
			$this->object->getUser(),
			$this->equalTo('someuser')
		);
	}

	/**
	 * Test the setUser method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetUser()
	{
		$this->object->setUser('root');

		$this->assertThat(
			$this->object->getUser(),
			$this->equalTo('root')
		);
	}

	/**
	 * Test the getPass method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPass()
	{
		$this->assertThat(
			$this->object->getPass(),
			$this->equalTo('somepass')
		);
	}

	/**
	 * Test the setPass method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetPass()
	{
		$this->object->setPass('secret');

		$this->assertThat(
			$this->object->getPass(),
			$this->equalTo('secret')
		);
	}

	/**
	 * Test the getHost method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetHost()
	{
		$this->assertThat(
			$this->object->getHost(),
			$this->equalTo('www.example.com')
		);
	}

	/**
	 * Test the setHost method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetHost()
	{
		$this->object->setHost('www.example.org');

		$this->assertThat(
			$this->object->getHost(),
			$this->equalTo('www.example.org')
		);
	}

	/**
	 * Test the getPort method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPort()
	{
		$this->assertThat(
			$this->object->getPort(),
			$this->equalTo('80')
		);
	}

	/**
	 * Test the setPort method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetPort()
	{
		$this->object->setPort('8888');

		$this->assertThat(
			$this->object->getPort(),
			$this->equalTo('8888')
		);
	}

	/**
	 * Test the getPath method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetPath()
	{
		$this->assertThat(
			$this->object->getPath(),
			$this->equalTo('/path/file.html')
		);
	}

	/**
	 * Test the setPath method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetPath()
	{
		$this->object->setPath('/this/is/a/path/to/a/file.htm');

		$this->assertThat(
			$this->object->getPath(),
			$this->equalTo('/this/is/a/path/to/a/file.htm')
		);
	}

	/**
	 * Test the getFragment method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetFragment()
	{
		$this->assertThat(
			$this->object->getFragment(),
			$this->equalTo('fragment')
		);
	}

	/**
	 * Test the setFragment method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testSetFragment()
	{
		$this->object->setFragment('someFragment');

		$this->assertThat(
			$this->object->getFragment(),
			$this->equalTo('someFragment')
		);
	}

	/**
	 * Test the isSsl method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testisSsl()
	{
		$object = new Uri('https://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$object->isSsl(),
			$this->equalTo(true)
		);

		$object = new Uri('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$object->isSsl(),
			$this->equalTo(false)
		);
	}

	public function queryParams()
	{
		return array(
			array(
				array('foo', 'bar', 'baz', 'boom', 'cow' => 'milk', 'php' => 'hypertext processor'),
				'0=foo&1=bar&2=baz&3=boom&cow=milk&php=hypertext+processor'
			),
			array(
				array(
					'user'     => array(
						'name' => 'Bob Smith',
						'age'  => 47,
						'sex'  => 'M',
						'dob'  => '5/12/1956'
					),
					'pastimes' => array('golf', 'opera', 'poker', 'rap'),
					'children' => array(
						'bobby' => array('age' => 12, 'sex' => 'M'),
						'sally' => array('age' => 8, 'sex' => 'F')
					),
					'CEO'
				),
				'user%5Bname%5D=Bob+Smith&user%5Bage%5D=47&user%5Bsex%5D=M&' .
				'user%5Bdob%5D=5%2F12%2F1956&pastimes%5B0%5D=golf&pastimes%5B1%5D=opera&' .
				'pastimes%5B2%5D=poker&pastimes%5B3%5D=rap&children%5Bbobby%5D%5Bage%5D=12&' .
				'children%5Bbobby%5D%5Bsex%5D=M&children%5Bsally%5D%5Bage%5D=8&children%5Bsally%5D%5Bsex%5D=F&0=CEO'
			),
		);
	}

	/**
	 * @testdox Query can be retrieved as a string suitable for use in URIs
	 *
	 * @param $array
	 * @param $string
	 *
	 * @dataProvider queryParams
	 */
	public function testBuildQueryFromArrayToString($array, $string)
	{
		$uri = new Uri();
		$uri->setQuery($array);

		$this->assertEquals($string, $uri->getQuery(false), 'Uri::getQuery() array -> string failed');
	}

	/**
	 * @testdox Query can be retrieved as an array with decoded values
	 *
	 * @param $array
	 * @param $string
	 *
	 * @dataProvider queryParams
	 */
	public function testBuildQueryArrayToArray($array, $string)
	{
		$uri = new Uri();
		$uri->setQuery($array);

		$this->assertEquals($array, $uri->getQuery(true), 'Uri::getQuery() array -> array failed');
	}

	/**
	 * @testdox Query can be retrieved as a string suitable for use in URIs
	 *
	 * @param $array
	 * @param $string
	 *
	 * @dataProvider queryParams
	 */
	public function testBuildQueryFromStringToString($array, $string)
	{
		$uri = new Uri();
		$uri->setQuery($string);

		$this->assertEquals($string, $uri->getQuery(false), 'Uri::getQuery() string -> string failed');
	}

	/**
	 * @testdox Query can be retrieved as an array with decoded values
	 *
	 * @param $array
	 * @param $string
	 *
	 * @dataProvider queryParams
	 */
	public function testBuildQueryStringToArray($array, $string)
	{
		$uri = new Uri();
		$uri->setQuery($string);

		$this->assertEquals($array, $uri->getQuery(true), 'Uri::getQuery() string -> array failed');
	}
}
