
# Nova Custom Views
This package allows you to define entirely customizable views for specific Nova Resources.

# Installation 

```
composer require devmtm/nova-custom-views
```

Then you need to add the service provider to your config/app.php **after the NovaServiceProvider**:

<pre>
 /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        ...
        App\Providers\NovaServiceProvider::class,
        ...
        <b>devmtm\NovaCustomViews\NovaCustomViewsServiceProvider::class,</b>
        ...
</pre>


Create a new view 
This is the same process as for any other Nova Tool, ResourceTool or Field. You can simply use this command in your terminal:

```
php artisan nova:views resourceName viewName
```

This will create your view component in /nova-components/views/resourceName. If you've installed the dependencies during the previous process, you can directly go ahead and use:

```
cd ./nova-components/views/resourceName && npm run watch
```

Allowed values for viewName are [index || lens || detail || create || update || attach || update-attached]

If you dont provide viewName, it will create all available nova views.

You can modify any view component inside /nova-components/views/resourceName/resources/js/views as you like