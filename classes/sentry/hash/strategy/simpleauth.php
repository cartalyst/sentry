<?php
/**
 * Part of the Sentry package for FuelPHP.
 *
 * @package    Sentry
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 - 2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

 namespace Sentry;

 class Hash_Strategy_SimpleAuth extends Hash_Driver
 {
 	/**
 	 * @var  object  hashing object
 	 */
 	protected $hasher  = null;

 	/**
 	 * Constructor
 	 * Sets configuration options
 	 *
 	 * @param  array  configuration options
 	 */
 	public function __construct($options)
 	{
 		$this->options = $options;
 	}

 	/**
	 * Creates a random salt and hashes the given password with the salt.
	 * String returned is prepended with a 16 character alpha-numeric salt.
	 *
	 * @param   string  Password to generate hash/salt for
	 * @return  string
	 */
 	public function create_password($password)
	{
		return $this->hash_password((string) $password);
	}

 	/**
	 * Checks the given password to see if it matches the one in the database.
	 *
	 * @param   string  Password to check
	 * @param   string  Hashed User Password
	 * @return  bool
	 */
 	public function check_password($password, $hashed_password)
	{
		return $this->hash_password((string) $password) == $hashed_password;
	}

	/**
	 * Hash a given password with the given salt.
	 *
	 * @param   string  Password to hash
	 * @param   string  Password Salt
	 * @return  string
	 */
 	protected function hash_password($password)
	{
		$salt = isset($this->options['salt']) ? $this->options['salt'] : '';

		return base64_encode($this->hasher()->pbkdf2($password, $salt, 10000, 32));
	}

	/**
	 * Returns the hash object and creates it if necessary
	 *
	 * @return  PHPSecLib\Crypt_Hash
	 */
	protected function hasher()
	{
		if ( ! class_exists('PHPSecLib\\Crypt_Hash', false))
		{
			import('phpseclib/Crypt/Hash', 'vendor');
		}
		is_null($this->hasher) and $this->hasher = new \PHPSecLib\Crypt_Hash();

		return $this->hasher;
	}
 }
