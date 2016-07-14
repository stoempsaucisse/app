<?php

/**
 * Microffice's basic routes. Should be extended by package...
 *
 *
 * @author Stoempsaucisse <stoempsaucisse@hotmail.com>
 */

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Disabled to force authentication middleware
// Route::get('/', function () {
//     return view('welcome');
// });

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    // Replacing
    // Route::auth();
    // with separted routes to disable registration
    // Authentication Routes...
    $this->get('login', 'Auth\AuthController@showLoginForm');
    $this->post('login', 'Auth\AuthController@login');
    $this->get('logout', 'Auth\AuthController@logout');
    // Registration Routes...
    // $this->get('register', 'Auth\AuthController@showRegistrationForm');
    // $this->post('register', 'Auth\AuthController@register');
    // Password Reset Routes...
    $this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    $this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    $this->post('password/reset', 'Auth\PasswordController@reset');

    /*
     * Routes that need authentication
     *
     **************************************************************************/
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/', 'HomeController@welcome');
        Route::get('/home', 'HomeController@index');
        // User routes
        Route::resource('user', 'UserController');
    });
});
