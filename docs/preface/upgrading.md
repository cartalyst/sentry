## Upgrading Sentry

[Upgrading from 2.1 to 3.0](#upgrading-2-1-to-3-0)

### Upgrading from 2.1 to 3.0 {#upgrading-2-1-to-3-0}

Upgrading from Sentry 2.1 to Sentry 3.0 has been made as seamless as possible, however depending on the level of customization you have provided to Sentry may involve a little work.

#### Upgrade Your Composer Dependency

The first thing you will need to do is change your `cartalyst/sentry` dependency from `2.1.*` to `3.0.*` in your application's `composer.json` file.

#### Reset configuration

The Sentry configuration file has been overhauled in version 3.0 for Laravel users. If you have published your configuration file, you will need to remove it and re-publish it.
