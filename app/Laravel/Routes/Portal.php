<?php

/*,'domain' => env("FRONTEND_URL", "wineapp.localhost.com")*/
Route::group(['as' => "portal.",
		 'namespace' => "Portal",
		 'middleware' => ["web"]
		],function() {


    Route::group(['prefix' => "",'as' => "auth."/*'middleware' => "portal.guest"*/], function(){
        Route::get('login',['as' => "login", 'uses' => "AuthenticationController@login"]);
    });


    Route::group([/*'middleware' => "portal.auth"*/], function(){
        Route::get('/',['as' => "index", 'uses' => "MainController@index"]);
    }); 
});