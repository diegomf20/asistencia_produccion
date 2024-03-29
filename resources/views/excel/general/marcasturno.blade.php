<html>
    {{-- {{ HTML::style('css/excel.css') }} --}}
    <table>
        <thead>
            <tr>
                <th ></th>
                <th><b>Razón social:</b></th>
                <th colspan="4">{{ $empresa->descripcion }}</th>
            </tr>
            <tr>
                <th></th>
                <th><b>RUC:</b></th>
                <th colspan="1">{{ $empresa->ruc }}</th>
            </tr>
            <tr>
                <th></th>
                <th><b>DIRECCIÓN:</b></th>
                <th colspan="4">{{ $empresa->direccion }}</th>
            </tr>
            <tr>
                <th ></th>
                <th><b>Fecha:</b></th>
                <th colspan="3">{{ $fecha }}</th>
            </tr>
            <tr></tr>
            <tr>
                <th>DNI</th>
                <th colspan="2">Apellidos y Nombres</th>
                <th>Marca 1</th>            
                <th>Marca 2</th>            
                <th>Marca 3</th>            
                <th>Marca 4</th>            
                <th>Marca 5</th>            
                <th>Marca 6</th>            
                <th>TOTAL</th>            
            </tr>
        </thead>
        <tbody>
            @php
                // dd($operadores);    
            @endphp
            @foreach ($operadores as $key=>$operador)
                <tr class="bordered">
                    <td>{{ $operador->dni }}</td>
                    <td colspan="2">{{ $operador->NombreApellido }}</td>
                    <td>{{ (count(explode("@",$operador->marcas))>=(0+1)) ? explode("@",$operador->marcas)[0] : '-' }}</td>
                    <td>{{ (count(explode("@",$operador->marcas))>=(1+1)) ? explode("@",$operador->marcas)[1] : '-' }}</td>
                    <td>{{ (count(explode("@",$operador->marcas))>=(2+1)) ? explode("@",$operador->marcas)[2] : '-' }}</td>
                    <td>{{ (count(explode("@",$operador->marcas))>=(3+1)) ? explode("@",$operador->marcas)[3] : '-' }}</td>
                    <td>{{ (count(explode("@",$operador->marcas))>=(4+1)) ? explode("@",$operador->marcas)[4] : '-' }}</td>
                    <td>{{ (count(explode("@",$operador->marcas))>=(5+1)) ? explode("@",$operador->marcas)[5] : '-' }}</td>
                    <td>{{ $operador->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</html>
