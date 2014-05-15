## Groups

### Create a group

Creating new Groups is very easy and in this section you will learn how to create one.

Param       | Required | Default | Type  | Description
----------- | -------- | ------- | ----- | -----------------------------------
$attributes | true     | null    | array | The group attributes.

Below we'll list all the valid `keys` that you can pass through the `$attributes`.

Key         | Required | Type    | Description
----------- | -------- | ------- | --------------------------------------------
name        | true     | string  | The name of the group.
permissions | false    | array   | The group permissions, pass a `key`/`value` pair.

#### Example

```php
try
{
	// Create the group
	$group = Sentry::createGroup(array(
		'name'        => 'Moderator',
		'permissions' => array(
			'admin' => 1,
			'users' => 1,
		),
	));
}
catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
{
	echo 'Name field is required';
}
catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
{
	echo 'Group already exists';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                          | Description
-------------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Groups\NameRequiredException      | If you don't provide the group name, this exception will be thrown.
Cartalyst\Sentry\Groups\GroupExistsException       | This exception will be thrown when the group you are trying to create already exists on your database.

### Update a group

#### Example

```php
try
{
	// Find the group using the group id
	$group = Sentry::findGroupById(1);

	// Update the group details
	$group->name = 'Users';
	$group->permissions = array(
		'admin' => 1,
		'users' => 1,
	);

	// Update the group
	if ($group->save())
	{
		// Group information was updated
	}
	else
	{
		// Group information was not updated
	}
}
catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
{
	echo 'Name field is required';
}
catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
{
	echo 'Group already exists.';
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                      | Description
---------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Groups\NameRequiredException  | If you don't provide the group name, this exception will be thrown.
Cartalyst\Sentry\Groups\GroupExistsException   | This exception will be thrown when the group you are trying to create already exists on your database.
Cartalyst\Sentry\Groups\GroupNotFoundException | If the provided group was not found, this exception will be thrown.

### Delete a Group

#### Example

```php
try
{
	// Find the group using the group id
	$group = Sentry::findGroupById(1);

	// Delete the group
	$group->delete();
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

#### Exceptions

Below is a list of exceptions that this method can throw.

Exception                                      | Description
---------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Groups\GroupNotFoundException | If the provided group was not found, this exception will be thrown.

### Finding Groups

Sentry provides simple methods to find you your groups.

#### Find all the Groups

This will return all the groups.

```php
$groups = Sentry::findAllGroups();
```

#### Find a group by its ID

Find a group by it's ID.

```php
try
{
	$group = Sentry::findGroupById(1);
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

#### Find a Group by it's Name

Find a group by it's name.

```php
try
{
	$group = Sentry::findGroupByName('admin');
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group was not found.';
}
```

#### Exceptions

Below is a list of exceptions that the methods can throw.

Exception                                      | Description
---------------------------------------------- | --------------------------------------------------------------------------------
Cartalyst\Sentry\Groups\GroupNotFoundException | If the provided group was not found, this exception will be thrown.

### Helpers

#### getPermissions()

Returns the permissions of a group.

```php
try
{
	// Find the group using the group id
	$group = Sentry::findGroupById(1);

	// Get the group permissions
	$groupPermissions = $group->getPermissions();
}
catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
{
	echo 'Group does not exist.';
}
```
