<?php
    use Illuminate\Support\Facades\Route;
    
    Route::get('/devs/logs', ['as'   => 'logs.viewer',
                              'uses' => '\Jsocha\LogsViewer\LogsViewerController@index']);