<?php

Route::group(['as' => "api.",
         'prefix' => "api",
         'namespace' => "Api",
         'middleware' => ["api", "api.valid_format"]
        ],function() {


    Route::group(['prefix' => 'auth', 'as' => "auth."],function(){
        Route::post('login.{format}',['as' => "login",'uses' => "AuthenticationController@authenticate"]);
        Route::post('logout.{format}',['as' => "logout",'uses' => "AuthenticationController@logout",'middleware' => "api.auth:api"]);

        Route::post('check-login.{format}',['as' => "check_login",'uses' => "AuthenticationController@check_login",'middleware' => "api.auth:api"]);
        Route::post('refresh-token.{format}',['as' => "refresh_token",'uses' => "AuthenticationController@refresh_token"]);

        Route::post('register.{format}',['as' => "store",'uses' => "AuthenticationController@store"]);
    });

    //authenticated  route
    Route::group(['middleware' => "api.auth:api"],function(){
        Route::group(['prefix' => "profile",'as' => 'profile.'],function(){
            Route::post('show.{format}',['as' => 'show', 'uses' => "ProfileController@show"]);
        });

        Route::group(['prefix' => "article",'as' => 'article.'],function(){
            Route::post('all.{format}',['as' => 'index', 'uses' => "ArticleController@index"]);
            Route::post('create.{format}',['as' => 'store', 'uses' => "ArticleController@store"]);
            Route::post('edit.{format}',['as' => 'update', 'uses' => "ArticleController@update", 'middleware' => ["api.exist:article", "api.exist:own_article"]]);
            Route::post('show.{format}',['as' => 'show', 'uses' => "ArticleController@show", 'middleware' => ["api.exist:article", "api.exist:own_article"]]);
            Route::post('delete.{format}',['as' => 'destroy', 'uses' => "ArticleController@destroy", 'middleware' => ["api.exist:article", "api.exist:own_article"]]);

        });
    });

});