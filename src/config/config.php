<?php

return array(

	'session' => 'cartalyst_sentry',

	'cookie' => 'cartalyst_sentry',

	'users' => array(

		'model' => 'Cartalyst\Sentry\Users\EloquentUser',

	),

	'checkpoints' => array('activation', 'swift', 'throttle'),

	'activation' => array(

		'model' => 'Cartalyst\Sentry\Activations\EloquentActivation',

		// Seconds - defaults to 3 days
		'expires' => 259200,

	),

	'swift' => array(

		'email' => null,
		'password' => null,
		'api_key' => null,
		'app_code' => null,

		'method' => 'swipe',

		'model' => 'Cartalyst\Sentry\Swift\EloquentSwift',

	),

	'throttling' => array(

		'model' => 'Cartalyst\Sentry\Throttling\EloquentThrottle',

		'global' => array(

			'interval' => 900,
			'thresholds' => array(
				10 => 1,
				20 => 2,
				30 => 4,
				50 => 8,
				50 => 16,
				60 => 12
			),

		),

		'ip' => array(

			'interval' => 900,

			'thresholds' => 5,

		),

		'user' => array(

			'interval' => 900,

			'thresholds' => 5,

		),

	),

);
