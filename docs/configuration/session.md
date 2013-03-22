#### Session

The properties that Sentry uses in the Session can be changed from their defaults
should you wish to do so. A reason for this could be if they are conflicting with
another session property you are using.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`user`                       | string          | sentry_user     | Sets the session key to retrieve the Sentry User.
`provider`                   | int             | sentry_provider | Sets the session key to retrieve the Sentry Provider.
