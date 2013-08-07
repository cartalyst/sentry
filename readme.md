# SentryLdap

SentryLdap is a fork of Cartalyst Sentry library. Added new features like ldap authentication.SentryLdap is a PHP 5.3+ fully-featured authentication & authorization system. It also provides additional features such as user groups and additional security features.

Sentry is a framework agnostic set of interfaces with default implementations, though you can substitute any implementations you see fit.

[![Build Status](https://travis-ci.org/cartalyst/sentry.png?branch=master)](https://travis-ci.org/cartalyst/sentry)

### Features

It also provides additional features such as user groups and additional security features:
- Ldap login

Sentry features
- Configurable authentication (can use any type of authentication required, such as username or email)
- Authorization
- Activation of user *(optional)*
- Groups and group permissions
- "Remember me"
- User suspension
- Login throttling *(optional)*
- User banning
- Password resetting
- User data
- Interface driven - switch out your own implementations at will

### Installation

Installation of SentryLdap is very easy. Add:

    "repositories": [
    {
        "type":"git",
        "url":"https://github.com/anndro/SentryLdap.git"
    }]

to your composer.json file then follow one of the following guides to get SentryLdap working with your favorite framework or on it's own:

1. Install in [Laravel 4](http://docs.cartalyst.com/sentry-2/installation/laravel-4)
2. Install in [FuelPHP 1](http://docs.cartalyst.com/sentry-2/installation/fuelphp-1)
3. Install in [CodeIgniter 3](http://docs.cartalyst.com/sentry-2/installation/codeigniter-3)

### Using

This library is still beta for ldap functions. You have to change this lines in your config file.

	'ldap' => array(
		'server'	=> 'ldapserver',
		'port'		=> 'ldapport'
	),

After this settings you have to follow sentry orginal document file. You can use ldap login like this;

    // Set login credentials
    $credentials = array(
        'userid'    => '03310412183',
        'password' => 'vnkuseyn',
    );

    // Try to authenticate the user
    $user = Sentry::authenticateWithLdap($credentials, false);

Also have Sentry::authenticateWithLdapAndRemember($credentials); function.


### Upgrading

Is not available yet, I am working on it.

### Support

We offer support through [our help forums](http://help.cartalyst.com), on [IRC at #cartalyst](http://webchat.freenode.net/?channels=cartalyst) for normal sentry issues, and through GitHub issues (bugs only) for Ldap issues.

