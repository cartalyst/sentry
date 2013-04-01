#### Database Instance

Sentry allows you to set a specific database instance for Sentry to connect to.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$db_instance`               | string          | null            | Name of database instance, or null for the default instance.

----------

#### Login Column

Sentry allows you to pick your login column to either be username or email. This
option is easy to adjust in the configuration.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`$login_column`              | string          | email           | Name of login column, either email or username.

> **Notes:** Whichever option you pick, email will always be required. Both will always need to be unique.

----------

#### Remember Me

Remember me sets a cookie to keep the user logged in for a designated length of
time. This feature contains the following options.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`cookie_name`                | string          | sentry_rm       | The cookies name.
`expiration`                 | int             | 20160           | The amount of time the cookie should be set for in minutes.

----------

#### Session

The properties that Sentry uses in the Session can be changed from their defaults
should you wish to do so. A reason for this could be if they are conflicting with
another session property you are using.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`user`                       | string          | sentry_user     | Sets the session key to retrieve the Sentry User.
`provider`                   | int             | sentry_provider | Sets the session key to retrieve the Sentry Provider.

----------

#### Suspension / Limit Attempts

Sentry has included an additional security feature to limit the amount of attempts
a user/ip combo can make within a certain timeframe.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`enabled`                    | bool            | true            | Used to enable/disable the suspension feature.
`attempts`                   | integer         | 5               | The number of attempts allowed before the user is suspended.
`time`                       | integer         | 15              | The length of time the account should be suspended for in minutes.

----------

#### Password Hashing

Sentry has its own Password Hashing Driver.  This allows you to easily create a new driver for your auth system to use or hook into.

The driver can be found in `sentry/sentry/hash/driver.php`. New drivers or `strategies` can be found in the `hash/strategy` directory.  A sample driver is provided below.

##### Example

	class Sentry_Hash_Strategy_Sentry extends Sentry_Hash_Driver
	{

		/**
		 * Constructor.
		 *
		 * @return void
		 */
		public function __construct($options)
		{

		}

		/**
		 * Creates a random salt and hashes the given password with the salt.
		 * String returned is prepended with a 16 character alpha-numeric salt.
		 *
		 * @param  string  Password to generate hash/salt for
		 * @return string
		 */
		public function create_password($password)
		{
			$salt = Str::random(16);

			return $salt.$this->hash_password($password, $salt);
		}

		/**
		 * Checks the given password to see if it matches the one in the database.
		 *
		 * @param  string  Password to check
		 * @param  string  Hashed User Password
		 * @return bool
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
		 * @param  string  Password to hash
		 * @param  string  Password Salt
		 * @return string
		 */
		protected function hash_password($password, $salt)
		{
			$password = hash('sha256', $salt.$password);

			return $password;
		}

	}

After your driver is created, all you need to do is modify your `sentry/config.php`
file accordingly in its `hash' array.
