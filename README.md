![Paul Felberbauer - Unsplash #tM16SjCYy84](https://images.unsplash.com/photo-1526814895543-b5be7268dd1e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1200&h=400&q=80)

# Laratraits [WIP]

Laratraits is a Laravel package containing useful traits and some classes to use along your Controllers, Service Providers, Commands and what not. Take a look!

## What it includes

Before installing, take a look into the list. If you're only using one, just copy and paste it in your project, no problem.

### General

* `PipesThrough`: Allows a class to be sent itself through a pipeline.
* `AuthorizesItself`: Allows to authorize an action on the object itself.
* `ValidatesItself`: Validates an incoming data using self-contained rules.
* `DispatchesItself`: Allows to dispatch the object instance to one of many Jobs previously set.
* `RendersFromMarkdown`: Takes a given class property to parse Markdown text and return HTML. Compatible with `Htmlable` interface.
* `DiscoverClasses`: Discovers classes inside a directory, optionally filtered by a method name.
* `Multitapable`: Makes a class infinitely tap-able. You can exit the tap using `->target` or a method name appended with `AndUntap`.
* `SavesToSession`: Saves the object (or part of it) to the session.
* `SavesToCache`: Saves the object (or part of it) to the cache.
* `SavesToStorage`: Saves the object (or part of it) to the storage.

### Models

* `AutoFill`: Automatically fills the Model with values by each method name, like `fillFooAttribute()`.
* `UsesUuid`: Automatically fills the UUID on the Model. Comes with an optional Eloquent Query Builder local scopes. You can override the UUID generation.
* `SelectsDefaultColumns`: Adds a `DefaultColumns` Global Scope to the Model that selects only given default columns, unless overrun manually in the query.
* `SoftCacheAccessors`: Saves the result of a accessor to avoid running the accessor logic again. Overrides the `mutateAttribute()` method.
* `DynamicallyMutates`: Cast an attribute based on what other attribute type says. Useful for columns that hols the data type, and other the raw data value.
* `EncryptsAttributes`: Automatically encrypts and decrypts attributes using a random key that the User must issue.

### Global Scopes

* `MacrosEloquent`: Automatically adds selective Macros to the Eloquent Builder instance itself, instead of globally, when using a Global Scope. Append a method with "macro" and return a Closure to use as macro.

### Controllers

* `ThrottlesRequest`: A customizable request throttler.
* `CacheKeysRequest`: Creates a digestible cache key based on a hash of the Controller Class, Request path and (optionally) the User authenticated. 

### Service Providers

* `RouteBindsClasses`: Explicitly bind a list of classes to the Router, allowing them to be instantiated by their value or using `fromRouteBinding()`. Classes need to implement `RouteBound`.
* `CallsDiscoveredClasses`: Loops through discovered classes from a path and runs a callable. 

## License

This package is open-sourced software licensed under the MIT license.
Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
