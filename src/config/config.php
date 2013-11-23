<?php

return array(

	'session' => 'cartalyst_sentry',

	'cookie' => 'cartalyst_sentry',

	'users' => array(

		'model' => 'Cartalyst\Sentry\Users\EloquentUser',

	),

	'checkpoints' => array('activation'/*, throttling */),

	'activation' => array(

		'model' => 'Cartalyst\Sentry\Activations\EloquentActivation',

		// Minutes
		'expires' => 4320,

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
