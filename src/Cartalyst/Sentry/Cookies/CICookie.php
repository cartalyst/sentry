<?php namespace Cartalyst\Sentry\Cookies;
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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use CI_Input as Input;

class CICookie implements CookieInterface {

	/**
	 * The CodeIgniter input object.
	 *
	 * @var \CI_Input
	 */
	protected $input;

	/**
	 * Cookie options.
	 *
	 * @var array
	 */
	protected $options = array(
		'name'   => 'cartalyst_sentry',
		'domain' => '',
		'path'   => '/',
		'prefix' => '',
		'secure' => false,
	);

	/**
	 * Create a new CodeIgniter cookie driver.
	 *
	 * @param  \CI_Input  $input
	 * @param  string|array  $options
	 * @param  string  $key
	 */
	public function __construct(Input $input, $options = array())
	{
		$this->input = $input;

		if (is_array($options))
		{
			$this->options = array_merge($this->options, $options);
		}
		else
		{
			$this->options['name'] = $options;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function put($value)
	{
		$options = array_merge($this->options, array(
			'value'  => serialize($value),
			'expire' => 2628000,
		));

		$this->input->set_cookie($options);
	}

	/**
	 * {@inheritDoc}
	 */
	public function get()
	{
		$value = $this->input->cookie($this->options['name']);

		if ($value)
		{
			return unserialize($value);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function forget()
	{
		$this->input->set_cookie(array(
			'name'   => $this->options['name'],
			'value'  => '',
			'expiry' => '',
		));
	}

}
