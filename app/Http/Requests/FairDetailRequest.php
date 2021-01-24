<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class FairDetailRequest extends FormRequest
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
            case 'POST':
                if (Route::currentRouteName() === 'fairDetail.createFairDetail') {
                    $rules = [
                        "fair_id" => [
                            "required",
                            "size:36",
                            "exists:fairs,fair_id"
                        ],
                        "append_information_id" => [
                            "required",
                            "size:36",
                            "exists:HospitalAppends,append_information_id",
                        ]
                    ];
                }
                break;
        }
        return $rules;
    }
}
