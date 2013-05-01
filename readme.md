# Laravel Test Helpers (Alpha!)

This package eases the process of writing tests for a Laravel application. Specifically, it provides a Factory creator, as well as a handful of model test helpers, like `assertValid` and `assertBelongsTo`.

## Installation

As usual, install this package through Composer.

```js
"require-dev": {
    "way/laravel-test-helpers": "dev-master"
}
```

## Factories

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

### Overrides

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

## Test Helpers

This package also includes a growing list of test helpers.

### assertValid and assertNotValid

```php
<?php

use Way\Tests\Factory;

class UserTest extends TestCase {
    use Way\Tests\ModelHelpers;

    public function testIsInvalidWithoutName()
    {
        $user = Factory::user(['name' => null]);

        $this->assertNotValid($user);
    }

}
```

All model test helpers are stored as trait. This makes them super easy to import into our test class. Simply add `use Way\Tests\ModelHelpers;` to top of the class, and you should be good to go.

In the example above, we are asserting that a User model should be invalid, unless its `name` field is not empty.

> Currently, the assertion will look for a `validate` method on the model. This may be changed to be more flexible in the future.

### Asserting Relationships

There are also various assertions for Laravel relationships. Let's assert that a User model has many Posts.

```php
public function testHasManyPosts()
{
    $this->assertHasMany('posts', 'User');
}
```

Running `phpunit` will return:

```bash
3) UserTest::testHasManyPosts
Expected the 'User' class to have method, 'posts'.
Failed asserting that false is true.
```

Go ahead and add a `posts` method to `User`.

```php
public function posts()
{
    $this->hasMany('Post');
}
```

And now we're back to green.

Currently, you can assert:

- `assertBelongsTo`
- `assertHasOne`
- `assertHasMany`