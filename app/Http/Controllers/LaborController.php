<?php

namespace App\Http\Controllers;

use App\Model\Labor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\LaborValidate;
use Illuminate\Support\Facades\DB;

class LaborController extends Controller
{
    /**
     * Visualiza todos los Labores
     */
    public function index(Request $request)
    {
        if ($request->all==true) {
            $Labores=Labor::all();
        }else{
            $Labores=Labor::paginate(8);
        }
        return response()->json($Labores);
    }

    /**
     * Registra un nuevo Labor
     */
    public function store(LaborValidate $request)
    {
        $Labor=new Labor();
        $Labor->codigo=$request->codigo;
        $Labor->nom_labor=$request->nom_labor;
        $Labor->area_id=$request->area_id;
        $Labor->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Labor Registrada"
        ]);
    }
    
    /**
     * Visualiza datos de un solo Labor
     */
    public function show($id)
    {
        $Labor=Labor::where('id',$id)->first();
        return response()->json($Labor);
    }
        
    public function update(LaborValidate $request, $id)
    {
        $Labor=Labor::where('id',$id)->first();
        $Labor->codigo=$request->codigo;
        $Labor->nom_labor=$request->nom_labor;    
        $Labor->area_id=$request->area_id;
        $Labor->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Labor Actualizada"
        ]);
    }

    /**
     * Cambiar de estado a la Labor
     */
    public function estado($id)
    {
        $Labor=Labor::where('id',$id)->first();
        $Labor->estado=($Labor->estado=='0') ? '1' : '0';
        $Labor->save();
        return response()->json([
            "status"=> "OK",
            "data"  => "Estado Actualizado"
        ]);
    }
}
