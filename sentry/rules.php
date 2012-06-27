<?php
/**
 * Part of the Sentry bundle for Laravel.
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
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
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

	protected static $bundle_rules = false;


	protected static function generate_rules()
	{
		// set rules array
		static::$rules = array();
		static::$bundle_rules = array();

		// get permissions file config options
		$permission_file = Config::get('sentry::sentry.permissions.file');

		// load global rules
		static::$rules = Config::get('sentry::sentry.permissions.rules');

		foreach (static::$rules as $rule)
		{
			static::$bundle_rules['sentry'][] = $rule;
		}

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
							static::$bundle_rules[$bundle][] = $rule;
						}
					}
				}
			}
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
							static::$bundle_rules[$bundle][] = $rule;
						}
					}
				}
			}
		}
	}

	/**
	 * Fetch needs rules
	 */
	public static function fetch_rules()
	{
		// generate rules if they are not set
		if ( ! static::$rules)
		{
			static::generate_rules();
		}

		return static::$rules;
	}

	/**
	 * Fetch needs rules
	 */
	public static function fetch_bundle_rules($bundle = null)
	{
		// generate rules if they are not set
		if ( ! static::$bundle_rules)
		{
			static::generate_rules();
		}

		// if a bundle was passed, only return that bundles rules
		if ($bundle)
		{
			return (array_key_exists($bundle, static::$bundle_rules)) ? static::$bundle_rules[$bundle] : array();
		}

		return static::$bundle_rules;
	}

}
