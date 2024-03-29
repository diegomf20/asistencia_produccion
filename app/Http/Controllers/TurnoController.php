<?php

namespace App\Http\Controllers;

use App\Model\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\TurnoValidate;
use Carbon\Carbon;

class TurnoController extends Controller
{
    /**
     * Visualiza todos los turnos
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $turnos=Turno::orderBy('id','DESC')->get();
        }else{
            $turnos=Turno::orderBy('id','DESC')->paginate(8);
        }
        return response()->json($turnos);
    }

    /**
     * Registra un nuevo turno
     */
    public function store(TurnoValidate $request)
    {
        $turno=new Turno();
        $turno->descripcion=Carbon::parse($request->fecha)->format('Ymd')."TURNO$request->horario";
        $turno->fecha=$request->fecha;
        $turno->horario=$request->horario;
        $turno->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Turno Registrado."
        ]);
    }
    
    /**
     * Visualiza datos de un solo turno
     */
    public function show($id)
    {
        $turno=Turno::where('id',$id)->first();
        return response()->json($turno);
    }
        
    public function update(turnoValidate $request, $id)
    {
        $turno=Turno::where('id',$id)->first();
        $turno->descripcion=$request->descripcion;
        $turno->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Turno Actualizada"
        ]);
    }

    /**
     * Cambiar de estado a la turno
     */
    // public function estado($id)
    // {
    //     $turno=Turno::where('id',$id)->first();
    //     $turno->estado=($turno->estado=='0') ? '1' : '0';
    //     $turno->save();
    //     return response()->json([
    //         "status"=> "OK",
    //         "data"  => "Estado Actualizado"
    //     ]);
    // }
}
