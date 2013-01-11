<?php
/**
 * Part of the Sentry Package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Cookies\NativeCookie;

class NativeCookieTest extends PHPUnit_Framework_TestCase {

    protected $cookie;

    /**
     * Setup test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->cookie = new NativeCookie();
    }

    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->cookie = null;
    }

    /**
     * @runInSeparateProcess
     */
    public function testPut()
    {
        $this->cookie->put('foo', 'bar', 123);
        $this->assertEquals('bar', $this->cookie->getValue());
    }

    /**
     * @runInSeparateProcess
     */
    public function testForever()
    {
        $this->cookie->forever('foo', 'bar');

        $timeTest = false;

        if ($this->cookie->getLifeTime() > time() + 60*60*24*31*12*1)
            $timeTest = true;

        $this->assertTrue($timeTest);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGet()
    {
        // Ensure default param is "null"
        $this->cookie->put('foo', 'bar', 123);
        $this->assertEquals('bar', $this->cookie->getValue());

        $this->cookie->forget('foo');
        $this->assertEquals(null, $this->cookie->getValue());
    }

    /**
     * @runInSeparateProcess
     */
    public function testForget()
    {
        $this->cookie->forget('foo');
        $this->assertEquals(null, $this->cookie->getValue());
    }

    /**
     * @runInSeparateProcess
     */
    public function testFlush()
    {
        $this->cookie->flush();
        $this->assertEquals(null, $this->cookie->getValue());
    }

}