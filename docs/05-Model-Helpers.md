# Model Helpers

### `assertValid and `assertNotValid`

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

All model test helpers are stored as a trait. This makes them super easy to import into our test class. Simply add `use Way\Tests\ModelHelpers;` to the top of the class, and you should be good to go.

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
