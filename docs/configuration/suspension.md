#### Suspension / Limit Attempts

Sentry has included an additional security feature to limit the amount of attempts
a user/ip combo can make within a certain timeframe.

Parameter                    | Type            | Default         | Description
:--------------------------- | :-------------- | :-------------- | :--------------
`enabled`                    | bool            | true            | Used to enable/disable the suspension feature.
`attempts`                   | integer         | 5               | The number of attempts allowed before the user is suspended.
`time`                       | integer         | 15              | The length of time the account should be suspended for in minutes.
