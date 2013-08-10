# Factories

Ever found yourself repeatedly creating test models over and over?

```php
$user = new User;
$user->email = 'foo@example.com'
$user->name = 'Joe';
```

This can very quickly flood your test classes. Instead, use a factory!

```php
<?php

use Way\Tests\Factory;

class UserTest extends TestCase {

    public function testBasicExample()
    {
        $user = Factory::attributesFor('User');
    }
}
```

Now, the `$user` variable will be equal to random data that fits the data types for each field. Something like:

```php
.array(6) {
  'id' =>
  NULL
  'name' =>
  string(3) "Kim"
  'email' =>
  string(15) "kim@example.com"
  'age' =>
  int(26)
  'created_at' =>
  string(19) "2013-05-01 02:21:49"
  'updated_at' =>
  string(19) "2013-05-01 02:21:49"
}
```

## Overrides

There will be times, though, when you need to specify values for some fields. This can be particularly helpful for validation, where, say, a model should be invalid, unless an email is provided.

```php
$user = Factory::attributesFor('User', ['email' => null]);
```

Any fields specified in the second argument will override the random defaults.

## Models

The static `attributesFor` method is great for fetching an array of attributes. If you want the full Laravel collection, then you have two options:

```php
$user = Factory::user();
```

This technique uses `callStatic` to allow for the readable syntax shown above. This works, if the model is in the global namespace, but if it's not, you'll want to use the `make` method.

```php
$user = Factory::make('Models\User');
```

If you happen to be working with a real test database, you can also use the `create` method, which will instantiate the model, fill it with dummy data, and save it to the db.

```php
$user = Factory::create('User');
```
