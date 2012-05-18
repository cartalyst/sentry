<?php
/**
 * Part of the Sentry package for Laravel.
 *
 * @package    Sentry
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */

// Autoload classes
Autoloader::namespaces(array(
    'Sentry' => Bundle::path('sentry'),
));

// Set the global alias for Sentry
Autoloader::alias('Sentry\\Sentry', 'Sentry');

Sentry::_init();
