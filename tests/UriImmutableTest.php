<?php
/**
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\UriImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\UriImmutable class.
 *
 * @since  1.0
 */
class UriImmuteableTest extends TestCase
{
	/**
	 * Object under test
	 *
	 * @var    UriImmutable
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
		$this->object = new UriImmutable('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');
	}

	/**
	 * Tests the __set method. Immutable objects will throw
	 * an exception when you try to change a property.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::__set
	 * @expectedException \BadMethodCallException
	 */
	public function test__set()
	{
		$this->object->uri = 'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment';
	}

	/**
	 * Test the __toString method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::__toString
	 */
	public function test__toString()
	{
		$this->assertThat(
			$this->object->__toString(),
			$this->equalTo('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment')
		);
	}

	/**
	 * Test the toString method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::toString
	 */
	public function testToString()
	{
		$this->assertThat(
			$this->object->toString(),
			$this->equalTo('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment')
		);
	}

	/**
	 * Test the hasVar method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::hasVar
	 */
	public function testHasVar()
	{
		$this->assertThat(
			$this->object->hasVar('somevar'),
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
	 * @covers  Joomla\Uri\UriImmutable::getVar
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
	 * Test the getQuery method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getQuery
	 */
	public function testGetQuery()
	{
		$this->assertThat(
			$this->object->getQuery(),
			$this->equalTo('var=value')
		);
	}

	/**
	 * Test the getScheme method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getScheme
	 */
	public function testGetScheme()
	{
		$this->assertThat(
			$this->object->getScheme(),
			$this->equalTo('http')
		);
	}

	/**
	 * Test the getUser method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getUser
	 */
	public function testGetUser()
	{
		$this->assertThat(
			$this->object->getUser(),
			$this->equalTo('someuser')
		);
	}

	/**
	 * Test the getPass method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getPass
	 */
	public function testGetPass()
	{
		$this->assertThat(
			$this->object->getPass(),
			$this->equalTo('somepass')
		);
	}

	/**
	 * Test the getHost method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getHost
	 */
	public function testGetHost()
	{
		$this->assertThat(
			$this->object->getHost(),
			$this->equalTo('www.example.com')
		);
	}

	/**
	 * Test the getPort method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getPort
	 */
	public function testGetPort()
	{
		$this->assertThat(
			$this->object->getPort(),
			$this->equalTo('80')
		);
	}

	/**
	 * Test the getPath method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getPath
	 */
	public function testGetPath()
	{
		$this->assertThat(
			$this->object->getPath(),
			$this->equalTo('/path/file.html')
		);
	}

	/**
	 * Test the getFragment method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::getFragment
	 */
	public function testGetFragment()
	{
		$this->assertThat(
			$this->object->getFragment(),
			$this->equalTo('fragment')
		);
	}

	/**
	 * Test the isSsl method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @covers  Joomla\Uri\UriImmutable::isSsl
	 */
	public function testisSsl()
	{
		$this->object = new UriImmutable('https://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->object->isSsl(),
			$this->equalTo(true)
		);

		$this->object = new UriImmutable('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');

		$this->assertThat(
			$this->object->isSsl(),
			$this->equalTo(false)
		);
	}

	/**
	 * Tests the withPath method with a non-string value.
	 * There should be an invalid argument exception thrown according to PSR-7.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPath
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithPathWithNonStringArgument()
	{
		$this->object->withPath(array('path' => '/path/differentFile.html'));
	}

	/**
	 * Tests the withPath method with a query appended to the path.
	 * There should be an invalid argument exception thrown according to PSR-7.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPath
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithPathWithQueryProvided()
	{
		$this->object->withPath('/path/differentFile.html?var=foo');
	}

	/**
	 * Tests the withPath method with a fragment.
	 * There should be an invalid argument exception thrown according to PSR-7.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPath
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithPathWithFragmentProvided()
	{
		$this->object->withPath('/path/differentFile.html#fragment');
	}

	/**
	 * Tests the withPath method with URL starting with a /.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPath
	 */
	public function testWithPathWithStartingSlash()
	{
		$newUri = $this->object->withPath('/path/differentFile.html');

		$this->assertEquals(
			$this->object->toString(),
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'Original object is different from previously'
		);

		$this->assertEquals(
			$newUri->toString(),
			'http://someuser:somepass@www.example.com:80/path/differentFile.html?var=value#fragment',
			'New object has incorrect URL'
		);
	}

	/**
	 * Tests the withPath method with an empty Path.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPath
	 */
	public function testWithPathWithEmptyPath()
	{
		$newUri = $this->object->withPath('');

		// Ensure the original object is unchanged
		$this->assertEquals(
			$this->object->toString(),
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'Original object is different from previously'
		);

		// Ensure the new object is changed correctly
		$this->assertEquals(
			$newUri->toString(),
			'http://someuser:somepass@www.example.com:80?var=value#fragment',
			'New object has incorrect URL'
		);
	}

	/**
	 * Tests the withPath method with URL starting with the same path.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPath
	 */
	public function testWithPathWithSamePath()
	{
		$newUri = $this->object->withPath('/path/file.html');

		$this->assertEquals(
			$newUri,
			$this->object,
			'If the new path is the same as the old path then the objects should be clones'
		);
	}

	/**
	 * Tests the withPort method with an integer port.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPort
	 */
	public function testWithPortWithInteger()
	{
		$newUri = $this->object->withPort(81);

		// Ensure the original object is unchanged
		$this->assertEquals(
			$this->object->toString(),
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'Original object is different from previously'
		);

		// Ensure the new object is changed correctly
		$this->assertEquals(
			$newUri->toString(),
			'http://someuser:somepass@www.example.com:81/path/file.html?var=value#fragment',
			'New object has incorrect URL'
		);
	}

	/**
	 * Tests the withPort method with an string port.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPort
	 */
	public function testWithPortWithStringInteger()
	{
		$newUri = $this->object->withPort('81');

		// Ensure the original object is unchanged
		$this->assertEquals(
			$this->object->toString(),
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'Original object is different from previously'
		);

		// Ensure the new object is changed correctly
		$this->assertEquals(
			$newUri->toString(),
			'http://someuser:somepass@www.example.com:81/path/file.html?var=value#fragment',
			'New object has incorrect URL'
		);
	}

	/**
	 * Tests the withPort method with an integer port.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPort
	 */
	public function testWithPortWithNull()
	{
		$newUri = $this->object->withPort(null);

		// Ensure the original object is unchanged
		$this->assertEquals(
			$this->object->toString(),
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'Original object is different from previously'
		);

		// Ensure the new object is changed correctly
		$this->assertEquals(
			$newUri->toString(),
			'http://someuser:somepass@www.example.com/path/file.html?var=value#fragment',
			'New object has incorrect URL'
		);
	}

	/**
	 * Tests the withPort method with a larger than allowed port.
	 * There should be an invalid argument exception thrown according to PSR-7.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPort
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithPortWithLargePortNumber()
	{
		$this->object->withPort(65536);
	}

	/**
	 * Tests the withPort method with a non-numeric port.
	 * There should be an invalid argument exception thrown according to PSR-7.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPort
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithPortWithNonNumericValue()
	{
		$this->object->withPort(array('port' => 81));
	}

	/**
	 * Tests the withPath method with URL starting with the same path.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withPort
	 */
	public function testWithPortWithSamePort()
	{
		$newUri = $this->object->withPort(80);

		$this->assertEquals(
			$newUri,
			$this->object,
			'If the new port is the same as the old port then the objects should be clones'
		);
	}

	/**
	 * Tests the withScheme method with the same schema as current.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withScheme
	 */
	public function testWithSchemeWithSameScheme()
	{
		$newUri = $this->object->withScheme('http');

		$this->assertEquals(
			$newUri,
			$this->object,
			'If the new port is the same as the old port then the objects should be clones'
		);
	}

	/**
	 * Tests the withScheme method with a non-string scheme.
	 * There should be an invalid argument exception thrown according to PSR-7.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withScheme
	 * @expectedException \InvalidArgumentException
	 */
	public function testWithSchemeWithNonStringScheme()
	{
		$this->object->withScheme(array('scheme' => 'https'));
	}

	/**
	 * Tests the withScheme method with a different scheme.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 * @covers  Joomla\Uri\UriImmutable::withScheme
	 */
	public function testWithSchemeWithDifferentScheme()
	{
		$newUri = $this->object->withScheme('https');

		// Ensure the original object is unchanged
		$this->assertEquals(
			$this->object->toString(),
			'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'Original object is different from previously'
		);

		// Ensure the new object is changed correctly
		$this->assertEquals(
			$newUri->toString(),
			'https://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment',
			'New object has incorrect URL'
		);
	}
}
