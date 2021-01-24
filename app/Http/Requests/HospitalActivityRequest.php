<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class HospitalActivityRequest extends FormRequest
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
            case 'GET':
                if (Route::currentRouteName() === 'hospitalActivity.getHospitalActivity') {
                    $rules = [
                    ];
                }
                break;
        }
        return $rules;
    }
}
