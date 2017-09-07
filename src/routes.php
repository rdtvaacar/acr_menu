<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'Acr\Menu\Controllers', 'prefix' => 'acr/menu'], function () {
        Route::post('/ara', 'AcrMenuController@menu_ara');
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
                Route::get('/users', 'AcrMenuController@users');
                Route::post('/users/search', 'AcrMenuController@users');
                Route::get('/users/search', 'AcrMenuController@users');
                Route::post('/users/role/update', 'AcrMenuController@role_update');
                Route::get('/admin/user/login', 'AcrMenuController@user_login');
                Route::post('/users/change/pw', 'AcrMenuController@change_user_pw');
            });
        });


    });
});