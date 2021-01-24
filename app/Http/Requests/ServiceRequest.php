<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        $method = $this->getMethod();
        switch ($method) {
            case 'DELETE':
                break;

            case 'GET':
                break;

            case 'POST':
                break;

            case 'PUT':
                break;
        }
        return $rules;
    }

    public function validationData() {
        return array_merge($this->all(),$this->route()->parameters());
    }
}
