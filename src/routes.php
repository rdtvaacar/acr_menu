<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'Acr\Menu\Controllers', 'prefix' => 'acr/menu'], function () {
        Route::get('/', 'AcrMenuController@index');
        Route::group(['middleware' => ['auth']], function () {
            Route::group(['middleware' => ['admin']], function () {
                Route::get('/product/new', 'AcrMenuController@new_product');
                Route::get('/menuler', 'AcrMenuController@menuler');
                Route::post('/duzenle', 'AcrMenuController@duzenle');
                Route::post('/update', 'AcrMenuController@update');
                Route::post('/delete', 'AcrMenuController@delete');
                Route::post('/siraAzalt', 'AcrMenuController@siraAzalt');
                Route::post('/siraCogalt', 'AcrMenuController@siraCogalt');


            });
        });


    });
});