<?php

// autoload classes
Autoloader::namespaces(array(
    'Sentry' => Bundle::path('sentry'),
));

// set the global alias for Sentry
Autoloader::alias('Sentry\\Sentry', 'Sentry');

Sentry::_init();
