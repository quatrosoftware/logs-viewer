[![Packagist License](https://poser.pugx.org/jsocha/logs-viewer/license.png)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://poser.pugx.org/jsocha/logs-viewer/version.png)](https://packagist.org/packages/jsocha/logs-viewer)
[![Total Downloads](https://poser.pugx.org/jsocha/logs-viewer/d/total.png)](https://packagist.org/packages/jsocha/logs-viewer)

## Simple Laravel Logs Viewer
Simple parsing Laravel Logs inspired by <a href="https://github.com/rap2hpoutre/laravel-log-viewer">rap2hpoutre</a> with some fixes

## Installation

Require this package with composer:

```shell
composer require jsocha/logs-viewer dev-master
```

After updating composer add service prowider in config/app.php


```php
Jsocha\LogsViewer\LogsViewerServiceProvider::class,
```

You also need some design so publish
```php 
php artisan vendor:publish
```

You need to change APP_LOG in .env file to 
```php 
APP_LOG=daily
```


After that just add in routes/web.php

```php 
Route::get('devs/logs/{file?}/{action?}', '\Jsocha\LogsViewer\LogsViewerController@index')->name('logs.viewer');
```

To see logs go to:

```php 
http://example.com/devs/logs
```
