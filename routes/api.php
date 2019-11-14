<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
header("Access-Control-Allow-Origin: *");

Route::resource('operador', 'OperadorController');
Route::post('operador/{id}/estado','OperadorController@estado')->name('operador.estado');

Route::resource('actividad', 'ActividadController');
Route::post('actividad/{id}/estado','ActividadController@estado')->name('actividad.estado');

Route::resource('turno', 'TurnoController');

Route::post('conteo','ConteoController@nuevo');
Route::get('conteo','ConteoController@reporte');
Route::get('conteoOperario','ConteoController@reporteOperario');