<?php

namespace \Fuel\Migrations;

class Install_Sentry_Auth {

	public function up()
	{
		\Config::load('sentry', true);

		\DBUtil::create_table(\Config::get('table.users', array(
			'id' => array('constraint' => 11, 'type' => 'int', 'auto_increment' => true),
			'username' => array('constraint' => 50, 'type' => 'varchar'),
			'email' => array('constraint' => 50, 'type' => 'varchar'),
			'password' => array('constraint' => 81, 'type' => 'varchar'),
			'password_reset_hash' => array('constraint' => 65, 'type' => 'varchar'),
			'last_login' => array('constraint' => 11, 'type' => 'int'),
			'updated_at' => array('constraint' => 11, 'type' => 'int'),
			'created_at' => array('constraint' => 11, 'type' => 'int'),
		), array('id'));
	}

	public function down()
	{
		\DBUtil::drop_table(\Config::get('table.users');
	}
}
