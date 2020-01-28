<?php

namespace App\Http\Controllers;

use App\Model\Tareo;
use App\Model\Operador;
use App\Model\Turno;
use App\Model\Marcador;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TareoController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $operador=Operador::where('dni',$request->codigo_barras)->first();
        if ($operador==null) {
            $operador=new Operador();
            $operador->dni=$request->codigo_barras;
            $operador->nom_operador="Nuevo";
            $operador->ape_operador="Trabajador";
            $operador->save();
        }
        $fecha_hoy=Carbon::now()->format('Y-m-d');
        $marcador=Marcador::where('codigo_operador',$operador->dni)
            ->where(DB::raw("DATE(ingreso)"),$fecha_hoy)
            ->where('fundo_id',$request->fundo_id)
            ->first();
        if ($marcador==null) {
            return response()->json([
                "status"    =>"WARNING",
                "data"  =>'Operador no marco Asistencia.'
            ]);
        }
        // dd($marcador); 
        
        $tareo=new Tareo();
        $tareo->codigo_operador=$operador->dni;
        $tareo->proceso_id=$request->proceso_id;
        $tareo->labor_id=$request->labor_id;
        $tareo->area_id=$request->area_id;
        $tareo->fundo_id=$request->fundo_id;
        $tareo->cuenta_id=$request->user_id;
        $tareo->fecha=Carbon::now();
        $tareo->save();
        return response()->json([
            "status"=> "OK",
            "data"  => $operador
        ]);
    }
}
