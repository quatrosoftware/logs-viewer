
## Simple Laravel Logs Viewer
Simple parsing Laravel Logs

## Installation

Require this package with composer:

```shell
composer require jsocha/logs-viewer dev-master
```

After updating composer add service prowider in config/app.php


```php
Jsocha\LogsViewer\LogsViewerServiceProvider::class,
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
