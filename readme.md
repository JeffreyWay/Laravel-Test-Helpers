# Laravel Test Helpers (Beta)

This package eases the process of writing tests for a Laravel application, by offering:

- A Factory utility (quickly create and populate models)
- Model test helpers (`assertValid`, `assertBelongsTo`, etc.)
- `Assert` and `Should` PHPUnit wrappers

## Installation

As usual, install this package through Composer.

```js
"require-dev": {
    "way/laravel-test-helpers": "dev-master"
}
```

Please note that this package requires [Mockery](https://packagist.org/packages/mockery/mockery).

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

All model test helpers are stored as trait. This makes them super easy to import into our test class. Simply add `use Way\Tests\ModelHelpers;` to the top of the class, and you should be good to go.

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

## Assert and Should

> [Prefer a video introduction](https://dl.dropbox.com/u/774859/GitHub-Repos/PHPUnit-Wrappers.mp4)?

This package also includes two wrappers (you can add more) around PHPUnit's assertion library. For example, rather than typing:

```php
$this->assertEquals(4, 2 + 2);
```

You can instead do:

```php
Assert::equals(4, 2 + 2);

# or
Should::equal(4, 2 + 2);
```

To allow for more readability, when using Should, you may prepend `be` or `have`, like so:

```php
Should::beTrue(true);
Should::haveCount(2, ['a', 'b']);
```

### Aliases

Additionally, you can register your own aliases.

```php
Should::getInstance()->registerAliases([
  'beCakesAndPies' => 'assertTrue'
]);

# or

Assert::getInstance()->registerAliases([
  'eq' => 'assertEquals'
]);
```

Now, you can use `Should::beCakesAndPies` and `Assert::eq` in your tests, and they will map to `assertTrue` and `assertEquals`, respectively.

### Usage

Within your test file, use your desired assertion wrapper (or both).

```php
<?php

use Way\Tests\Should;

class DemoTest extends PHPUnit_Framework_TestCase {
  public function testItWorks()
  {
    Should::beTrue(true);
  }
}
```

And that's it! Here's a few examples:

```php
<?php

use Way\Tests\Should;
use Way\Tests\Assert;

class DemoTest extends PHPUnit_Framework_TestCase {
  public function testItWorks()
  {
    Should::beTrue(true);
    Assert::true(true);

    Should::equal(4, 2 + 2);
    Should::eq(4, 2 + 2);
    Assert::equals(4, 2 + 2);
    Assert::eq(4, 2 + 2);

    Should::beGreaterThan(20, 21);
    Assert::greaterThan(20, 21);

    Should::contain('Joe', ['John', 'Joe']);
    Should::have('Joe', ['John', 'Joe']);
    Assert::has('Joe', ['John', 'Joe']);
    Assert::has('Joe', 'Joey');

    Should::haveCount(2, ['1', '2']);
    Assert::count(2, ['1', '2']);
  }
}
```

Remember: these are simple wrappers around PHPUnit's assertions. Refer to the sidebar [here](http://www.phpunit.de/manual/current/en/index.html) for a full list.