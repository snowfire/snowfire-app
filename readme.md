## Snowfire App

This package makes it possible to connect your Laravel app to Snowfire.

## Laravel Compatibility

This package is compatible with Laravel 5 through Laravel 12.

## Install the package

Add this to your composer.json

```json
"snowfire/snowfire-app": "dev-master"
```

Or install via Composer:

```bash
composer require snowfire/snowfire-app
```

### Laravel 5

Add this to your service providers in `config/app.php`

```php
'Snowfire\App\SnowfireServiceProvider'
```

### Laravel 6+

Service providers are auto-discovered in Laravel 6+.

Add this to your route middlewares in `app/Http/Kernel.php`

```php
'snowfire' => \Snowfire\App\Middleware\SnowfireMiddleware::class,
'snowfireAdmin' => \Snowfire\App\Middleware\SnowfireAdminMiddleware::class,
```

Publish the config file

    $ php artisan vendor:publish

Create the database table for snowfire installations

    $ php artisan migrate


## Seed data

Add this to your `DatabaseSeeder.php`

    $this->call('\Snowfire\App\Storage\Seeder');

## CSRF protection

If you have CSRF middleware activated in `app/Http/Kernel.php` open `app/Http/Middleware/VerifyCsrfToken.php` and add the following to the handle method:

```php
if ($request->header('User-Agent') == 'Snowfire')
{
	return $next($request);
}
```

## Integration possibilities

There are two different ways to connect your app to Snowfire. As a link in the admin area and as a public action.

### Example

Lets say you have a list of events. A public action will be something like `http://your-app.com/events/all` which will render an HTML `<ul>` list. Then you will have an admin link from Snowfire to `http://your-app.com/admin` which will let users add/edit/remove events.

Start by adding your actions to `config/snowfire_app.php`

```php
return [
	'id' => 'demo_app',
	'name' => 'Demo app',
	'tab' => 'admin',
	'actions' => [
		'events' => 'action.events.index',
		'event' => 'action.events.show',
	]
];
```

This config adds the admin link as a named route called `snowfire.tab` and a route for all events. Both tab and actions are optional (but you need one of them, right?)

#### Your `routes.php`

```php

// Admin routes
Route::group(['prefix' => 'admin/{snowfireAppId}', 'before' => 'snowfireAppAuth', 'namespace' => 'Admin'], function()
{
	Route::get('/', ['as' => 'admin.index', function($appId)
    {
        return 'Admin for appId: ' . $appId;
    }]);

});

// Action routes
Route::group(['namespace' => 'Action'], function()
{
	Route::get('events', ['uses' => 'EventsController@index', 'as' => 'action.events.index']);
	Route::get('events/{id}', ['uses' => 'EventsController@event', 'as' => 'action.events.show']);
});
```

This creates an admin route and the public action. The admin route is behind a snowfireAppAuth filter which makes sure the user is logged in and trusted.

### Your Snowfire snippet to the public event list

Login to Snowfire and install the app (System -> Apps)

	http://your-hosted-app.com/snowfire/install

Create a new snippet with this code:

```javascript
<?xml version="1.0" encoding="utf-8"?>
<snippet>
    <name>All events</name>
    <html><![CDATA[
		{ com_application2 (
            id: '{{ component_id_0 }}',
            description: 'Demo App',
            app: 'demo_app',
            action: 'events',
            debug: 'true'
        ) }
	]]></html>
</snippet>
```

**Warning:** Adding applications to the root / (i.e the home page) is currently not supported. Please create a sub page to add your app.

Now just add the snippet to a page and it will show you a list of events.

## Links

When you are working in an action that will be rendered within Snowfire, you need to use:

```html
<a href="{{ Snowfire::route('my.route') }}">A linked route</a>
```

This will make sure the links works from within Snowfire. 
