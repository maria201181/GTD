<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', ['middleware' => 'auth', function () {
    return view('home');
}]); */

Route::auth();

Route::get('/','HomeController@index');

Route::get('/home', 'HomeController@index');

Route::get('/summary', 'HomeController@summary');

Route::get('/reportExcel', 'HomeController@reportExcel');

Route::resource('user', 'UserController');

Route::resource('company', 'CompanyController');

Route::resource('reportGeneral', 'ReportGeneralController');

Route::resource('loadMassive', 'LoadMassiveController');

Route::put('/user/disabled/{id}', 'UserController@disabled');

Route::put('/company/disabled/{id}', 'CompanyController@disabled');

Route::get('/account', 'AccountController@index');

//Route::get('/loadMassive', 'LoadMassiveController@index');

//Route::post('/load', 'LoadMassiveController@load');
