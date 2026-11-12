<?php

/**
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\UriImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Joomla\Uri\UriImmutable class.
 */
class UriImmutableTest extends TestCase
{
    /**
     * Object under test
     *
     * @var  UriImmutable
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        $this->object = new UriImmutable('http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment');
    }

    public function test__set()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->object->uri = 'http://someuser:somepass@www.example.com:80/path/file.html?var=value#fragment';
    }

    public function testWithersReturnAModifiedCopy()
    {
        $original = (string) $this->object;

        $uri = $this->object
            ->withScheme('https')
            ->withUserInfo('user', 'secret')
            ->withHost('example.org')
            ->withPort(8443)
            ->withPath('/a/./b/../c')
            ->withQuery('q=a%26b&r=1')
            ->withFragment('top');

        $this->assertInstanceOf(UriImmutable::class, $uri);
        $this->assertNotSame($this->object, $uri);
        $this->assertSame('https://user:secret@example.org:8443/a/c?q=a%26b&r=1#top', (string) $uri);
        $this->assertSame('a&b', $uri->getVar('q'));
        $this->assertSame($original, (string) $this->object);
    }

    public function testWithersRemoveParts()
    {
        $uri = $this->object->withUserInfo('')->withPort(null)->withQuery([])->withFragment('');

        $this->assertSame('http://www.example.com/path/file.html', (string) $uri);
    }

    public function testWithQueryAcceptsAnArray()
    {
        $uri = $this->object->withQuery(['a' => '1', 'b' => '2']);

        $this->assertSame('a=1&b=2', $uri->getQuery());
        $this->assertSame(['a' => '1', 'b' => '2'], $uri->getQuery(true));
    }

    public function testCopyCannotBeConstructedAgain()
    {
        $this->expectException(\BadMethodCallException::class);

        $this->object->withHost('example.org')->__construct('http://example.net/');
    }
}
