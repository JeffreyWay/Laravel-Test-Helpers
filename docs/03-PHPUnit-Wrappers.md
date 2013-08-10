# Assert and Should

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
