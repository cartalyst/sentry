### Delete a Group

The delete method deletes the current group and all associations to it.

returns `bool`

throws `Sentry\SentryException`

----------

#### Example

	try
	{
		// Delete the group
		if (Sentry::group(4)->delete())
		{
			// Group was deleted
		}
		else
		{
			// Group was not deleted
		}
	}
	catch (Sentry\SentryException $e)
	{
		$errors = $e->getMessage();
	}
