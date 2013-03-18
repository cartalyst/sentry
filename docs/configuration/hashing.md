###Password Hashing

----------

Sentry has its own Password Hashing Driver.  This allows you to easily create a new driver for your auth system to use or hook into.

The driver can be found in `sentry/sentry/hash/driver.php`. New drivers or `strategies` can be found in the `hash/strategy` directory.  A sample driver is provided below.

Example:

	class Sentry_Hash_Strategy_Sentry extends Sentry_Hash_Driver
	{

		/**
		 * set constructor
		 */
		public function __construct($options) {}

		/**
		 * Creates a random salt and hashes the given password with the salt.
		 * String returned is prepended with a 16 character alpha-numeric salt.
		 *
		 * @param   string  Password to generate hash/salt for
		 * @return  string
		 */
		public function create_password($password)
		{
			$salt = Str::random(16);

			return $salt.$this->hash_password($password, $salt);
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
			// grabs the salt from the current password
			$salt = substr($hashed_password, 0, 16);

			// hash the inputted password
			$password = $salt.$this->hash_password($password, $salt);

			// check to see if passwords match
			return $password == $hashed_password;
		}

		/**
		 * Hash a given password with the given salt.
		 *
		 * @param   string  Password to hash
		 * @param   string  Password Salt
		 * @return  string
		 */
		protected function hash_password($password, $salt)
		{
			$password = hash('sha256', $salt.$password);

			return $password;
		}

	}

After your driver is created, all you need to do is modify your `sentry/config.php` file accordingly in its `hash' array.
