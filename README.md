![Paul Felberbauer - Unsplash #tM16SjCYy84](https://images.unsplash.com/photo-1526814895543-b5be7268dd1e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darkghosthunter/laratraits.svg?style=flat-square)](https://packagist.org/packages/darkghosthunter/laratraits) [![License](https://poser.pugx.org/darkghosthunter/laratraits/license)](https://packagist.org/packages/darkghosthunter/laratraits)
![](https://img.shields.io/packagist/php-v/darkghosthunter/laratraits.svg)
 ![](https://github.com/DarkGhostHunter/Laratraits/workflows/PHP%20Composer/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Laratraits/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Laratraits?branch=master)

# Laratraits

Laratraits is a Laravel package containing useful traits and some classes to use along your Models, Controllers, Service Providers and what not. Take a look!

## What it includes

Before installing, take a look into the list. If you're only using one, just copy and paste it in your project, no problem, as each trait and file includes a copy of the MIT License.

### General

* [`DiscoverClasses`](src/DiscoverClasses.php): Discovers classes inside a directory, optionally filtered by a method name or interface.
* [`DispatchesItself`](src/DispatchesItself.php): Allows to dispatch the object instance to one of many Jobs previously set.
* [`Multitaps`](src/Multitaps.php): Makes a class infinitely tap-able. You can exit the tap using `->target` or a method name appended with `AndUntap`.
* [`PipesThrough`](src/PipesThrough.php): Allows a class to be sent itself through a pipeline.
* [`RendersFromMarkdown`](src/RendersFromMarkdown.php): Takes a given class property to parse Markdown text and return HTML. Compatible with `Htmlable` interface.
* [`ValidatesItself`](src/ValidatesItself.php): Validates an incoming data using self-contained rules.
* [`SavesToSession`](src/SavesToSession.php): Saves the object (or part of it) to the session.
* [`SavesToCache`](src/SavesToCache.php): Saves the object (or part of it) to the cache..php
* [`SavesToStorage`](src/SavesToStorage.php): Saves the object (or part of it) to the storage.

### Models

* [`AutoFill`](src/Models/AutoFill.php): Automatically fills the Model with values by each method name, like `fillFooAttribute()`.
* [`UsesUuid`](src/Models/UsesUuid.php): Automatically fills the UUID on the Model. Comes with an optional Eloquent Query Builder local scopes. You can override the UUID generation.
* [`DefaultColumns`](src/Models/DefaultColumns.php): Adds a `DefaultColumns` Global Scope to the Model that selects only given default columns, unless overrun manually in the query.
* [`SoftCachesAccessors`](src/Models/SoftCachesAccessors.php): Saves the result of a accessor to avoid running the accessor logic again. Overrides the `mutateAttribute()` method.
* [`DynamicallyMutates`](src/Models/DynamicallyMutates.php): Cast an attribute based on what other attribute type says. Useful for columns that hols the data type, and other the raw data value.
* [`NeighbourRecords`](src/Models/NeighbourRecords.php): Allows to easily get the "next" and "previous" record from a given model.
* [`HasSlug`](src/Models/HasSlug.php): Allows a Model to be bound to routes using the slug like `this-is-the-model`. Requires a new column in the table.
* [`ModelType`](src/Models/ModelType.php): Useful for Models that share a single table but a different "column type".
* [`HasFile`](src/Models/HasFile.php): Associates a single file to the Model. The File contents is automatically saved when model is persisted/updated. Hash checking is always done. 

### Global Scopes

* [`MacrosEloquent`](src/Scopes/MacrosEloquent.php): Automatically adds selective Macros to the Eloquent Builder instance itself, instead of globally, when using a Global Scope. Append a method with "macro" and return a Closure to use as macro.

### Controllers

* [`ThrottlesRequests`](src/Controllers/ThrottlesRequests.php): An automatic and customizable request throttler, much like the default `ThrottlesLogins` trait.

### Middleware

* [`CacheStaticResponse`](src/Middleware/CacheStaticResponse.php): Caches (hopefully) static responses, avoiding running the controller logic, for a given time.
* [`ShareAuthenticatedUser`](src/Middleware/ShareAuthenticatedUser.php): Shares the authenticated user across all views.
* [`ValidateConsumableSignature`](src/Middleware/ValidateConsumableSignature.php): Makes signed routes work only one time except on client or server errors.

## Installing

Just fire up composer and that's it.

    composer require darkghosthunter/laratraits

## Usage

Just check any of these traits. Each trait and other classes contains an brief explanation on how to use in the first lines.

If you want to use one, [just do it](https://www.php.net/manual/en/language.oop5.traits.php).

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\SavesToCache;
use DarkGhostHunter\Laratraits\Models\UsesUuid;

class Post extends Model
{
    use UsesUuid;
    use SavesToCache;
    
    // ...
}
```

Some traits may instance other classes for advanced logic if necessary, like the `DiscoverClasses` trait.

> There is no application overhead since there is no Service Provider registered.

## Missing a trait?

You can make an issue with your proposal. Consider the logic must be contained inside a trait. PRs are preferred with tests.

## License

This package is open-sourced software licensed under the MIT license.

Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
