[![Latest Stable Version](https://poser.pugx.org/digitalcloud/nova-custom-views/v/stable)](https://packagist.org/packages/digitalcloud/nova-custom-views)
[![Total Downloads](https://poser.pugx.org/digitalcloud/nova-custom-views/downloads)](https://packagist.org/packages/digitalcloud/nova-custom-views)
[![Monthly Downloads](https://poser.pugx.org/digitalcloud/nova-custom-views/d/monthly)](https://packagist.org/packages/digitalcloud/nova-custom-views)
[![Daily Downloads](https://poser.pugx.org/digitalcloud/nova-custom-views/d/daily)](https://packagist.org/packages/digitalcloud/nova-custom-views)

# Nova Custom Views
This package allows you to create customizable views for specific Nova resources.

# Installation 

```
composer require digitalcloud/nova-custom-views
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
php artisan nova:views resource-name view-name
```

This will create your view component in /nova-components/views/resource-name. If you've installed the dependencies during the previous process, you can directly go ahead and use:

```
cd ./nova-components/views/resource-name && npm run watch
```

Allowed values for `view-name` are [index | lens | detail | create | update | attach | update-attached]

If you dont provide `view-name`, it will create all previous nova views.

You can modify any view component inside /nova-components/views/resource-name/resources/js/views

# Example Usage: Creating custom detail view for user resource

To create a new view, say user detail view, you can run the command:
```
php artisan nova:views user detail
```
This will create a new path: '/nova-components/views/user' which contains all generated user views.
The new view is extended the default user nova detail view (using vue mixins). 
You can modify the default view by going to the path: '/nova-components/views/user/resources/js/views/Detail.vue' and add your custom code as the following:
```
<template>
   <div>YOUR_CODE_HERE</div>
</template>
```

# Example Usage: Creating Custom Dashboard
To create a custom dashboard view, you can run the command:
```
php artisan nova:dashboard
```
This will create a new path: '/nova-components/views/dashboard' which contains the custom dashboard component.
The new view is extended the default nova dashboard view (using vue mixins). 
You can modify the dashboard view by going to the path: '/nova-components/views/user/resources/js/views/Dashboard.vue' and add your custom code as the following:
```
<template>
   <div>YOUR_CODE_HERE</div>
</template>
```
