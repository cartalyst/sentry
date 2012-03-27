<?php
/**
 * Migrate Sentry Package to Version 2.0
 *
 * @package    Sentry
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 -2012 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Fuel\Migrations;

\Package::load('sentry');

/**
 * @author Daniel Berry
 */

class Migrate_To_Version_Two
{
	public function up()
	{
		// load the sentry config file
		\Config::load('sentry', true);

		// remove group table columns is_admin, parent and level
		\DBUtil::drop_fields(\Config::get('sentry.table.groups'), array('level', 'is_admin', 'parent'));

		// add group table column permissions
		\DBUtil::add_fields(\Config::get('sentry.table.groups'), array(
			'permissions' => array('type' => 'text'),
		));

		// add user table column permissions
		\DBUtil::add_fields(\Config::get('sentry.table.users'), array(
			'permissions' => array('type' => 'text'),
		));
	}

	public function down()
	{
		// load the sentry config file
		\Config::load('sentry', true);

		// add group table columns level, is_admin and parent
		\DBUtil::add_fields(\Config::get('sentry.table.groups'), array(
			'level'    => array('constraint' => 11,  'type' => 'int'),
			'is_admin' => array('constraint' => 1,   'type' => 'tinyint'),
			'parent' => array('constraint' => 11, 'type' => 'int'),
		));

		// remove group table column permission
		\DBUtil::drop_fields(\Config::get('sentry.table.groups'), array('permissions'));
	}
}