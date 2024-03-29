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
use App\Exports\HorasSemanaTrabajadorExport;
use App\Exports\MarcasTurnoTrabajadorExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Model\Operador;
use Peru\Http\ContextClient;
use Peru\Jne\{Dni, DniParser};

header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Origin: *");

Route::get('privilegios','CuentaController@verPrivilegios')->middleware('auth.token');
Route::post('privilegios','CuentaController@privilegios')
->name('cuenta.privilegios');
Route::resource('cuenta', 'CuentaController');

Route::post('login','CuentaController@login')->name('cuenta.login');
Route::get('operador/unitarios', 'OperadorController@unitarios');
Route::post('operador/masivo', 'OperadorController@masivo');
Route::resource('operador', 'OperadorController');
Route::post('operador/{id}/estado','OperadorController@estado')->name('operador.estado');
Route::resource('procedencia', 'ProcedenciaController');
Route::resource('planilla', 'PlanillaController');
Route::post('planilla/{id}/estado','PlanillaController@estado')->name('planilla.estado');
Route::resource('cargo', 'CargoController');
Route::resource('modulo', 'ModuloController');

Route::get('rol/{id}/modulos', 'RolController@showModulos');
Route::post('rol/{id}/modulos', 'RolController@updateModulos');
Route::resource('rol', 'RolController');

Route::get('fundo/proceso','FundoController@proceso')->name('fundo.proceso');
Route::resource('fundo', 'FundoController');
Route::get('area/labor','AreaController@labor')->name('area.labor');
Route::resource('area', 'AreaController');
Route::post('area/{id}/estado','AreaController@estado')->name('area.estado');

Route::resource('labor', 'LaborController');
Route::post('labor/{id}/estado','LaborController@estado')->name('labor.estado');

Route::post('proceso/{id}/estado','ProcesoController@estado')->name('proceso.estado');
Route::resource('proceso', 'ProcesoController')->middleware('auth.token');

Route::resource('linea', 'LineaController');
Route::post('linea/{id}/estado','LineaController@estado')->name('linea.estado');

Route::resource('turno', 'TurnoController');

Route::post('marcador/offline','MarcadorController@storeOffline')
    ->name('marcador.offline')->middleware('auth.token');
    // ->middleware('auth.token');
Route::post('marcador2','MarcadorController@store2')->name('marcador.store2')->middleware('auth.token');
Route::get('marcador/add','MarcadorController@add')->name('marcador.add')->middleware('auth.token');
Route::get('marcador/remove','MarcadorController@remove')->name('marcador.remove')->middleware('auth.token');
Route::resource('marcador', 'MarcadorController')->middleware('auth.token');


Route::post('tareo', 'TareoController@store')->name('tareo.store')->middleware('auth.token');

Route::get('reporte-rotaciones', 'ReporteController@rotaciones');
Route::get('reporte-turno', 'ReporteController@turno');
Route::get('reporte-semana', 'ReporteController@semana');
Route::get('reporte-semana-partida', 'ReporteController@semana_partida');
Route::get('reporte-pendientes', 'ReporteController@pendientes')->middleware('auth.token');
Route::get('reporte/pendientes-regularizar', 'ReporteController@pendientesRegularizar');
Route::get('reporte-marcas', 'ReporteController@marcas');
Route::get('v2/reporte-semana-partida', 'ReporteController@semana_partidaV2');
Route::get('v2/reporte-marcas', 'ReporteController@marcasV2');
Route::get('reporte-marcas/{codigo}', 'ReporteController@marcasXCodigo');

Route::post('conteo','ConteoController@nuevo');
Route::get('conteo','ConteoController@reporte');
Route::get('conteoOperario','ConteoController@reporteOperario');


Route::get('jne/dni/{dni}', 'OperadorController@jne');

Route::get('/horas-semana/{datos}', function ($datos) {
    $datoArray=explode("-",$datos);
    $anio=$datoArray[0];
    $semana=$datoArray[1];
    $planilla_id=($datoArray[2]=="") ? 0 : $datoArray[2];
    $fundo_id=($datoArray[3]=="") ? 0 : $datoArray[3];
    return Excel::download(new HorasSemanaTrabajadorExport($anio,$semana,$planilla_id,$fundo_id), "horas-semana-$anio-$semana.xlsx");
});

Route::get('/marcas-tuno/{fecha}/{turno}/{planilla}', function ($fecha,$turno,$planilla) {
    return Excel::download(new MarcasTurnoTrabajadorExport($fecha,$turno,$planilla), "marcas-turno-$fecha-$turno-$planilla.xlsx");
});

Route::get('rpt/horas_nocturnas','ReporteController@horas_nocturnas');
Route::get('rpt/rango','ReporteController@rango');
Route::get('v2/rpt/rango','ReporteController@rangoV2');

/**
 * Para trabajadores
 */
Route::get('/consulta/marcas', function ($id) {
    
});

Route::get('sincronizar/proceso',"SincronizarController@proceso");
Route::get('sincronizar/labor',"SincronizarController@labor");
Route::get('sincronizar/area',"SincronizarController@area");
Route::get('sincronizar/asistencia',"SincronizarController@asistencia");
Route::resource('configuracion',"ConfiguracionController");

Route::get('nisira/periodo', 'NisiraController@periodo');

/**
 * Ingreso
 */
ini_set('max_execution_time', 10*30);
Route::post('sincronizar/fotos',"SincronizarController@fotos");
Route::post('sincronizar/tareo',"SincronizarController@tareo");
Route::post('sincronizar/marcador',"SincronizarController@marcador");

//Input
Route::post('sincronizar/in/marcas',"SincronizarController@marcas");

Route::get('jne-masivo', function () {
    
    $operadores=Operador::where('nom_operador','Nuevo')->limit(30)->get();
    // dd($operadores);
    foreach ($operadores as $key => $operador) {
        $cs = new Dni(new ContextClient(), new DniParser());
        $person = $cs->get($operador->dni);
        if (!$person) {
            
        }else{
            // dd("hola");
            $operador->nom_operador=strtoupper(utf8_decode($person->nombres));        
            $operador->ape_operador=strtoupper(utf8_decode($person->apellidoPaterno." ".$person->apellidoMaterno));
            $operador->save();
        }
    }
});
