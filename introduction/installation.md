# Installation

The best and easiest way to install Sentry is with [Composer](http://getcomposer.org).

## Preparation

Open your `composer.json` file and add the following to the `require` array:

	"cartalyst/sentry": "2.1.*"

> **Note:** Make sure that after the required changes your `composer.json` file is valid by running `composer validate`.

## Install the dependencies

Run Composer to install or update the new requirement.

	php composer install

or

	php composer update

Now you are able to require the `vendor/autoload.php` file to autoload the package.
