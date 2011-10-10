<?php

/**
 * Part of the Sentry package for Fuel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace \Fuel\Migrations;

class Install_Sentry_Auth {

	public function up()
	{
		\Config::load('sentry', true);

		\DBUtil::create_table(\Config::get('table.users'), array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'username' => array('constraint' => 50, 'type' => 'varchar'),
			'email' => array('constraint' => 50, 'type' => 'varchar'),
			'password' => array('constraint' => 81, 'type' => 'varchar'),
			'password_reset_hash' => array('constraint' => 24, 'type' => 'varchar'),
			'temp_password' => array('constraint' => '81', 'type' => 'varchar'),
			'last_login' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
			'status' => array('constraint' => 25, 'type' => 'varchar'),
			'suspended_timestamp' => array('constraint' => 11, 'type' => 'int'),
		), array('id'));

		\DBUtil::create_table(\Config::get('table.suspended'), array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'login_id' => array('constraint' => 50, 'type' => 'varchar'),
			'ip' => array('constraint' => 25), 'type' => 'varchar'),
			'last_attempt_at' => array('constraint') => 11, 'type' => 'int'),
			'suspended_at' => array('constraint' => 11, 'type' => 'int'),
			'unsuspend_at' => array('constraint' => 11, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table(\Config::get('table.users'));
		\DBUtil::drop_table(\Config::get('table.suspended'));
	}
}
