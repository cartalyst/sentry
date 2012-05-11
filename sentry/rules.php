<?php
/**
 * Part of the Sentry package for Laravel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 - 2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Sentry;

use Bundle;
use Config;

/**
 * Sentry Auth Attempt Class
 */
class SentryRulesException extends SentryException {}

class Sentry_Rules
{
	protected static $rules = false;

	/**
	 * Fetch needs rules
	 */
	public static function fetch_rules()
	{
		if (static::$rules)
		{
			return static::$rules;
		}

		// set rules array
		static::$rules = array();

		// get permissions file config options
		$permission_file = Config::get('sentry::sentry.permissions.file');

		// load global rules
		static::$rules = Config::get('sentry::sentry.permissions.rules');

		// see if type is config
		if ( $permission_file['type'] == 'config' or empty($permission_file['type']) or $permission_file['type'] == null)
		{
			foreach (Bundle::$bundles as $bundle => $values)
			{
				$bundle_rules = Config::get($bundle.'::permissions.rules');

				if ( ! empty($bundle_rules))
				{
					// add rules to the rules array if they don't exist already
					foreach ($bundle_rules as $rule)
					{
						if ( ! in_array($rule, static::$rules))
						{
							static::$rules[] = $rule;
						}
					}
				}
			}

			return static::$rules;
		}

		// The type was not a config, need to find the set file and see if it exists.
		foreach (Bundle::$bundles as $bundle => $values)
		{
			// Set the path to the file according to the config
			$path = Bundle::path($bundle).Config::get('sentry::sentry.permissions.file.path');
			$path = str_finish($path, '/');

			// get the file name
			$file = Config::get('sentry::sentry.permissions.file.name').EXT;

			// if the file exists pull in rules if they are set
			if (file_exists($path.$file) )
			{
				$info = require $path.$file;

				if (isset($info['rules']))
				{
					// add rules to the rules array if they don't exist already
					foreach ($info['rules'] as $rule)
					{
						if ( ! in_array($rule, static::$rules))
						{
							static::$rules[] = $rule;
						}
					}
				}
			}
		}

		return static::$rules;
	}
}
