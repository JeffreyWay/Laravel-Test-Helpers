# Controller Helpers

This package contains a small list of controller-specific helper methods. Stored as a trait, include them, like so:

```php
<?php

class ExampleTest extends TestCase {
    use Way\Tests\ControllerHelpers;
}
```

## Methods

### `see`

Use the `see` or `assertSee` method to verify text content for a given response.

```php
public function testFindHelloOnHomePage()
{
    $this->call('GET', '/');
    
    $this->see('Hello World');
}
```

Optionally, you may specify an HTML container. For example, to search for *My Post* within an `h1` tag... 

```php
$this->see('My Post', 'h1');
