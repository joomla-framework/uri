<?php

/**
 * @copyright  Copyright (C) 2005 - 2022 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Uri\Tests;

use Joomla\Uri\UriHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \Joomla\Uri\UriHelper class.
 */
class UriHelperTest extends TestCase
{
    /**
     * @testdox  Ensure parse_url() parses a URL correctly
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlCorrectly()
    {
        $url = 'http://localhost/joomla_development/j16_trunk/administrator/index.php?option=com_contact&view=contact&layout=edit&id=5';

        $this->assertEquals(parse_url($url), UriHelper::parse_url($url));
    }

    /**
     * @testdox  Ensure parse_url() parses a URL with all options correctly
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlWithAllOptionsCorrectly()
    {
        $url = 'https://john:doe@www.google.com:80/folder/page.html#id?var=kay&var2=key&true';

        $this->assertEquals(parse_url($url), UriHelper::parse_url($url));
    }

    /**
     * @testdox  Ensure parse_url() parses a URL with UTF-8 characters correctly
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlWithUTF8CharactersCorrectly()
    {
        // Test special characters in URL
        $url      = 'http://joomla.org/mytestpath/È';
        $expected = parse_url($url);

        // Fix up path for UTF-8 characters
        $expected['path'] = '/mytestpath/È';
        $actual           = UriHelper::parse_url($url);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox  Ensure parse_url() parses a URL with UTF-8 characters correctly even with non utf-8 LCTYPE
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlWithUTF8CharactersAndWrongLcTypeCorrectly()
    {
        // Set a non utf-8 LCTYPE
        $previousLcType = setlocale(LC_CTYPE, '0');
        setlocale(LC_CTYPE, 'en_GB');

        // Test special characters in URL
        $url      = 'http://mydomain.com/mytestpath/中文/纹身馆简介你好/媒体报道';
        $expected = parse_url($url);

        // Fix up path for UTF-8 characters
        $expected['path'] = '/mytestpath/中文/纹身馆简介你好/媒体报道';
        $actual           = UriHelper::parse_url($url);

        // Return to previous LCTYPE
        setlocale(LC_CTYPE, $previousLcType);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox  Ensure parse_url() parses a URL with special characters correctly
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlWithSpecialCharactersCorrectly()
    {
        $url = 'http://mydomain.com/!*\'();:@&=+$,/?%#[]" \\';

        $this->assertEquals(parse_url($url), UriHelper::parse_url($url));
    }

    /**
     * @testdox  Ensure parse_url() parses a URL with encoded characters correctly
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlWithEncodedCharactersCorrectly()
    {
        $url = 'http://mydomain.com/%21%2A%27%28%29%3B%3A%40%26%3D%24%2C%2F%3F%25%23%5B%22%20%5C';

        $this->assertEquals(parse_url($url), UriHelper::parse_url($url));
    }

    /**
     * @testdox  Ensure parse_url() parses a URL with mixed characters correctly
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlParsesAUrlWithMixedCharactersCorrectly()
    {
        $url      = 'http://john:doe@mydomain.com:80/%È21%25È3*%(';
        $expected = parse_url($url);

        // Fix up path for UTF-8 characters
        $expected['path'] = '/%È21%25È3*%(';
        $actual           = UriHelper::parse_url($url);
        $url              = 'http://mydomain.com/!*\'();:@&=+$,/?%#[]" \\';

        $this->assertEquals($expected, $actual);
    }

    /**
     * @testdox  Ensure parse_url() returns false on an invalid URL
     *
     * @covers   Joomla\Uri\UriHelper
     */
    public function testEnsureParseUrlReturnsFalseOnAnInvalidUrl()
    {
        $url = 'http:///mydomain.com';

        $this->assertFalse(UriHelper::parse_url($url));
    }

    public function testParseUrlWithUtf8CharactersKeepsThePortAnInteger()
    {
        $this->assertSame(
            ['scheme' => 'https', 'host' => 'bücher.de', 'port' => 8080, 'path' => '/ü', 'query' => 'x=1'],
            UriHelper::parse_url('https://bücher.de:8080/ü?x=1')
        );
    }

    public static function utf8ComponentProvider(): \Generator
    {
        yield 'host' => [\PHP_URL_HOST, 'bücher.de'];
        yield 'port' => [\PHP_URL_PORT, 8080];
        yield 'path' => [\PHP_URL_PATH, '/ü'];
        yield 'query' => [\PHP_URL_QUERY, 'x=1'];
        yield 'missing fragment' => [\PHP_URL_FRAGMENT, null];
    }

    #[DataProvider('utf8ComponentProvider')]
    public function testParseUrlWithUtf8CharactersReturnsASingleComponent(int $component, string|int|null $expected)
    {
        $this->assertSame($expected, UriHelper::parse_url('https://bücher.de:8080/ü?x=1', $component));
    }
}
