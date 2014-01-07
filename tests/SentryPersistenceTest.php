<?php namespace Cartalyst\Sentry\Tests;
/**
 * Part of the Sentry package.
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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Persistence\SentryPersistence;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class SentryPersistenceTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testCheckWithNoSessionOrCookie()
	{
		$persistence = new SentryPersistence($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$session->shouldReceive('get')->once();
		$cookie->shouldReceive('get')->once();
		$this->assertNull($persistence->check());
	}

	public function testCheckWithSession()
	{
		$persistence = new SentryPersistence($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$session->shouldReceive('get')->once()->andReturn('foo');
		$this->assertEquals('foo', $persistence->check());
	}

	public function testCheckWithCookie()
	{
		$persistence = new SentryPersistence($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$session->shouldReceive('get')->once();
		$cookie->shouldReceive('get')->once()->andReturn('bar');
		$this->assertEquals('bar', $persistence->check());
	}

	public function testAdd()
	{
		$persistence = new SentryPersistence($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$persistable = m::mock('Cartalyst\Sentry\Persistence\PersistableInterface');
		$persistable->shouldReceive('generatePersistenceCode')->once()->andReturn('code');
		$session->shouldReceive('put')->with('code')->once();
		$persistable->shouldReceive('addPersistenceCode')->once();
		$persistence->add($persistable);
	}

	public function testAddAndRemember()
	{
		$persistence = new SentryPersistence($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$persistable = m::mock('Cartalyst\Sentry\Persistence\PersistableInterface');
		$persistable->shouldReceive('generatePersistenceCode')->once()->andReturn('code');
		$session->shouldReceive('put')->with('code')->once();
		$cookie->shouldReceive('put')->with('code')->once();
		$persistable->shouldReceive('addPersistenceCode')->once();
		$persistence->addAndRemember($persistable);
	}

	public function testRemove()
	{
		$persistence = m::mock('Cartalyst\Sentry\Persistence\SentryPersistence[check]');
		$persistence->__construct($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$persistable = m::mock('Cartalyst\Sentry\Persistence\PersistableInterface');
		$persistence->shouldReceive('check')->once()->andReturn('code');
		$session->shouldReceive('forget')->once();
		$cookie->shouldReceive('forget')->once();
		$persistable->shouldReceive('removePersistenceCode')->once()->andReturn('code');
		$persistence->remove($persistable);
	}

	public function testFlush()
	{
		$persistence = m::mock('Cartalyst\Sentry\Persistence\SentryPersistence[check]');
		$persistence->__construct($session = m::mock('Cartalyst\Sentry\Sessions\SessionInterface'), $cookie = m::mock('Cartalyst\Sentry\Cookies\CookieInterface'));
		$persistable = m::mock('Cartalyst\Sentry\Persistence\PersistableInterface');
		$session->shouldReceive('forget')->once();
		$cookie->shouldReceive('forget')->once();
		$persistable->shouldReceive('getPersistenceCodes')->once()->andReturn(array('code1', 'code2'));
		$persistable->shouldReceive('removePersistenceCode')->once()->andReturn('code1');
		$persistable->shouldReceive('removePersistenceCode')->once()->andReturn('code2');
		$persistence->flush($persistable);
	}

}
