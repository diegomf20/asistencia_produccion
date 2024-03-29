<?php

namespace App\Exports;

use App\Model\Operador;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// class HorasSemanaTrabajadorExport implements FromCollection
class MarcasTurnoTrabajadorExport implements FromView, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $fecha;
    private $turno;
    private $planilla;
 
    public function __construct(String $fecha,$turno,$planilla)
    {
        $this->fecha = $fecha;
        $this->turno = $turno;
        $this->planilla = $planilla;
    }

    public function columnFormats(): array
    {
        return [
            // 'B' => NumberFormat::FORMAT_TEXT
        ];
    }

    public function view(): View
    {
        $resultado=Operador::select(
            'dni',
            'nom_operador',
            'ape_operador',
            DB::raw('GROUP_CONCAT(CONCAT_WS("@",DATE_FORMAT(marcador.ingreso, "%d/%m/%Y %H:%i"),DATE_FORMAT(marcador.salida, "%d/%m/%Y %H:%i")) ORDER BY marcador.ingreso ASC SEPARATOR "@") AS marcas'),
            DB::raw('ROUND(SUM(TIMESTAMPDIFF(MINUTE,marcador.ingreso,IF(marcador.salida is null,marcador.ingreso,marcador.salida))/60 ),2) AS total')
        )->join('marcador','operador.dni','=','marcador.codigo_operador')
        // ->where(DB::raw("fecha_ref"),$request->fecha)
        ->where(DB::raw("fecha_ref"),$this->fecha)
        ->where('marcador.turno',$this->turno) 
        ->where('planilla_id',$this->planilla) 
        ->whereNotNull('ingreso')      
        ->groupBy('operador.dni')->get();
        return view('excel.marcastrabajador', [
            'operadores' => $resultado
        ]);
    }
}
