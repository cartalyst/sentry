<?php
/**
 * Part of the Sentry package for FuelPHP.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  2011 Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Fuel\Migrations;

\Package::load('sentry');

class Add_Group_Parent_Column {

	public function up()
	{
		\Config::load('sentry', true);

		\DBUtil::add_fields(\Config::get('sentry.table.groups'), array(
		    'parent' => array('constraint' => 11, 'type' => 'int'),
		));
	}

	public function down()
	{
		\Config::load('sentry', true);

		\DBUtil::drop_fields(\Config::get('sentry.table.groups'), array('parent'));
	}
}
