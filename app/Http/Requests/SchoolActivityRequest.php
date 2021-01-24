<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class SchoolActivityRequest extends FormRequest
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
                if (Route::currentRouteName() === 'schoolActivity.getSchoolActivity') {
                    $rules = [
                    ];
                }
                break;
        }
        return $rules;
    }
}
