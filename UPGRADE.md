Upgrading from v1.x to v2.x

## Removed

The following traits and classes has been removed:

*  `ClassDiscoverer`,
*  `DiscoverClasses`,
*  `DispatchesItself`,
*  `HasFile`.
*  `SoftCachesAccessors`,
*  `ThrottlesRequest`,

For `HasFile`, migrate to [Spatie's Laravel Medialibrary](https://github.com/spatie/laravel-medialibrary) package instead.

For `ThrottlesRequest`, migrate to [Laravel Rate Limiting](https://github.com/laravel/framework/pull/32726) once it becomes available.

## Changed 

### Namespace change

* The `Laratraits\Models` namespace has been changed to `Laratraits\Eloquent`.

## AutoFill trait changed

* The trait has been renamed to `FillsAttributes`

* The trait will now call Mutators when filling automatically a property.

## DefaultColumns trait changed

* It now incorporates the `withoutDefaultColumns()` method to disable the global scope that is applied automatically.

## DynamicallyMutates removed

* The `DynamicallyMutates` trait has been removed. Use [Custom Casts](https://laravel.com/docs/eloquent-mutators#custom-casts) instead.  

### Enumerable changed

* The `Enumerable` class incorporates a new method called `assign` that assigns a given state, alternatively to calling dynamically a method call.

### HasSlug changed

* The `attributeToSlug` method has changed to `sluggableAttribute` and now returns the string `title`.

* The `setSlugAttribute` method has changed to `slugValue`, and returns the slug value.

* The `initializeHasSlug` method has been removed.

* The `setSlug` method has been added. It automatically sets the slug attribute with the slug value from a given attribute value.  

* The slug will be created on saving the model only when the sluggable attribute has been modified.

* Routing now is optional. To disable routing by the slug, declare the `$routeBySlug` property as `false`.

## MacrosEloquent changed

* Now the trait will automatically re-route all Eloquent builder macros to the public static methods in the Scope. There is no longer need to return closures or what not.

> This can have up to 100% more performance and makes the code more readable.

### ModelType changed

* The `getQualifiedTypeColumn` method has been changed to `getModelTypeColumn`.

* The `getTypeName` method has been changed to `getModelType`, and now returns the class name as snake case by default.

### NeighbourRecords changed

* The trait now gets the latest and oldest records from the given model, using the creation timestamp of these.

* It now includes the `filterNeighbourQuery` that allows to further filter the records to retrieve.

* It also includes the `queryColumns` to filter which columns to retrieve. It defaults to only the key used for routing (which is the primary key by default)

## ValidateConsumableSignature changed

* The middleware no longer uses `terminate`, but rather, evaluates the response before sending it to the browser. This shrinks the window where a user can make two times the same request.

## UsesUuid changed

* The `getQualifiedUuidColumn()` is now `getUuidColumn()`.

* The `shouldAddGlobalScope` is now `addUuidGlobalScope()`.

## UuidScope changed

* Now all the scopes has been changed to public static methods to follow the changes on the `MacrosEloquent` trait.
