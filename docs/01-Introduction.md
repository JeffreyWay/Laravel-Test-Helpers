# Laravel Test Helpers

This package eases the process of writing tests for a Laravel application by offering:

- A Factory utility (quickly create and populate models)
- Model test helpers (`assertValid`, `assertBelongsTo`, etc.)
- Controller test helpers (`assertSee`)
- `Assert` and `Should` PHPUnit wrappers  

## Installation

As usual, install this package through Composer.

```js
"require-dev": {
    "way/laravel-test-helpers": "dev-master"
}
```

Please note that this package requires [Mockery](https://packagist.org/packages/mockery/mockery).
