<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OperadorEditar extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function validationData()
    {
        $data = parent::validationData();
        foreach($data as $key=>$dat)
        {
            if ($dat=="null") {
                $data[$key]=null;
            }
        }
        return $data;
    }
    
    public function rules()
    {
        return [
            'nom_operador' => 'required',
            'ape_operador' => 'required',
        ];
    }
    
    protected function failedValidation(Validator $validator) {
        //extraer array
        $sin_array=str_replace(["[","]"], "",json_encode($validator->errors()));
        
        throw new HttpResponseException(response()->json([
            "status" => "VALIDATION",
            "data"   =>  json_decode($sin_array)
        ], 200));
    }
}
