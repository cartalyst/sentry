###Suspension / Limit Attempts

----------

Sentry has included an additional security feature to limit the amount of attempts a user/ip combo can make within a certain timeframe.

<table>
	<tr>
		<th>Option</th>
		<th>Type</th>
		<th>Default</th>
		<th>Description</th>
	</tr>
	<tr>
		<th>enabled</th>
		<th>bool</th>
		<th>true</th>
		<th>Used to enable/disable the suspension feature.</th>
	</tr>
	<tr>
		<th>attempts</th>
		<th>integer</th>
		<th>5</th>
		<th>The number of attempts allowed before the user is suspended.</th>
	</tr>
	<tr>
		<td>time</td>
		<td>integer</td>
		<td>15</td>
		<td>The length of time the account should be suspended for in minutes..</td>
	</tr>
</table>
