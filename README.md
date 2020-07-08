![Paul Felberbauer - Unsplash #tM16SjCYy84](https://images.unsplash.com/photo-1526814895543-b5be7268dd1e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darkghosthunter/laratraits.svg?style=flat-square)](https://packagist.org/packages/darkghosthunter/laratraits) [![License](https://poser.pugx.org/darkghosthunter/laratraits/license)](https://packagist.org/packages/darkghosthunter/laratraits)
![](https://img.shields.io/packagist/php-v/darkghosthunter/laratraits.svg)
 ![](https://github.com/DarkGhostHunter/Laratraits/workflows/PHP%20Composer/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Laratraits/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Laratraits?branch=master)

# Laratraits

Laratraits is a Laravel package containing useful traits and some classes to use along your Models, Controllers, Service Providers and what not. Take a look!

## Requirements

* Laravel 7.
* PHP 7.2.15 or later.

## [Upgrade Guide from 1.x to 2.x](UPGRADE.md)

## Installation

Fire up Composer and that's it.

    composer require darkghosthunter/laratraits

This package doesn't use any Service Provider.

## Usage

Just check any of these traits. Each one and other classes contains a brief explanation on how to use in the first lines.

If you want to use one, [just do it](https://www.php.net/manual/en/language.oop5.traits.php).

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\SavesToCache;
use DarkGhostHunter\Laratraits\Eloquent\UsesUuid;

class Post extends Model
{
    use UsesUuid;
    use SavesToCache;
    
    // ...
}
```

## What it includes

Before installing, take a look into the list. If you're only using one, just copy and paste it in your project, no problem, as each trait and file includes a copy of the MIT License.

Just remember to **change the namespace** if you're copy-pasting them!

### Traits for everything

* [`Comparable`](src/Comparable.php): Allows a class or its properties to be compared to a list of values.
* [`ConditionCalls`](src/ConditionCalls.php): Allows an object instance to execute `when` and `unless` logic.
* [`EnumerableStates`](src/EnumerableStates.php): Allows a class instance to have a single allowed state.
* [`FiresItself`](src/FiresItself.php): Allows an Event to be fired conveniently.
* [`Multitaps`](src/Multitaps.php): Makes all class methods _chainable_, like using `tap()` but forever. You can exit the tap using `->target` or a method name appended with `AndUntap`.
* [`PipesThrough`](src/PipesThrough.php): Allows a class to be piped through a pipeline immediately or to a queue.
* [`RendersFromMarkdown`](src/RendersFromMarkdown.php): Takes a given class property to parse Markdown text and return HTML. Compatible with `Htmlable` interface.
* [`SavesToCache`](src/SavesToCache.php): Saves the object (or part of it) to the cache.
* [`SavesToSession`](src/SavesToSession.php): Saves the object (or part of it) to the session.
* [`SavesToStorage`](src/SavesToStorage.php): Saves the object (or part of it) to the storage.
* [`SecurelyJsonable`](src/SecurelyJsonable.php): Adds a signature to the Jsonable object that is checked to at unserialization to avoid tampering.
* [`SendsToHttp`](src/SendsToHttp.php): Sends the object (or part of it) through an HTTP Request.
* [`ThrottleMethods`](src/ThrottleMethods.php): Throttles a given method in a class transparently.
* [`ValidatesItself`](src/ValidatesItself.php): Validates an incoming data using self-contained rules.

### Useful classes

* [`Enumerable`](src/Enumerable.php): Lists and controls a state from a list. Useful for [casting enums](https://laravel.com/docs/eloquent-mutators#custom-casts).

### Models

* [`ConditionFill`](src/Eloquent/ConditionFill.php): Fills an attribute if a given condition is truthy or falsy.
* [`DefaultColumns`](src/Eloquent/DefaultColumns.php): Adds a `DefaultColumns` Global Scope to the Model selecting only given default columns, unless overrun manually in the query.
* [`EncryptsJson`](src/Eloquent/EncryptsJson.php): Encrypts and decrypts the JSON representation of a Model.
* [`FillsAttributes`](src/Eloquent/FillsAttributes.php): Automatically fills the Model with values by each method name, like `fillFooAttribute()`.
* [`HasSlug`](src/Eloquent/HasSlug.php): Allows a Model to be bound to routes using the slug like `this-is-the-model`. Must use an exclusive slug column in the model table.
* [`ModelType`](src/Eloquent/ModelType.php): Useful for Models that share a single table but have different "types", like Publications: Article, Post, Note, etc.
* [`NeighbourRecords`](src/Eloquent/NeighbourRecords.php): Allows to easily get a complete "next" and "previous" record from a given model, without using pagination.
* [`UsesUuid`](src/Eloquent/UsesUuid.php): Automatically fills the UUID on the Model. Comes with an optional Eloquent Query Builder local scopes. You can override the UUID generation.

#### Casts

* [`CastEnumerable`](src/Eloquent/Casts/CastEnumerable.php): Allows a string or integer column to be [_casted_](https://laravel.com/docs/eloquent-mutators#custom-casts) as Enumerable inside a model.
* [`CastsRepository`](src/Eloquent/Casts/CastRepository.php): Allows an json column to be [_casted_](https://laravel.com/docs/eloquent-mutators#custom-casts) as a Repository (like a config tree).

### Global Scopes

* [`MacrosEloquent`](src/Scopes/MacrosEloquent.php): Automatically adds selective Macros to the Eloquent Builder instance itself, instead of globally, when using a Global Scope. Append `macro` to a public static method and that's it, done.

### Middleware

* [`CacheStaticResponse`](src/Middleware/CacheStaticResponse.php): Caches static responses, avoiding running the controller logic, for a given time.
* [`ShareAuthenticatedUser`](src/Middleware/ShareAuthenticatedUser.php): Shares the authenticated user across all views.
* [`ValidateConsumableSignature`](src/Middleware/ValidateConsumableSignature.php): Makes [signed routes](https://laravel.com/docs/urls#signed-urls) work only one time, except on client or server errors.

### Blade

* [`RegistersFileDirective`](src/Blade/RegistersFileDirective.php): Easily register a directive using a PHP file contents.

## Missing a trait?

You can make an issue with your proposal. Consider the logic must be contained inside a trait, or use an auxiliar class to avoid polluting the class with multiple methods. PRs are preferred with tests.

## License

This package is open-sourced software licensed under the MIT license.

Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
