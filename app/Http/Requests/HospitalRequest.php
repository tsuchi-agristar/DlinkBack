<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use App\Models\Organization;
use App\Models\Hospital;
class HospitalRequest extends FormRequest
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
                if (Route::currentRouteName() === 'hospital.deleteHospitalById') {
                    $rules = [
                        "hospital_id" => [
                            "size:36"
                        ]
                    ];
                }
                break;

            case 'GET':
                if (Route::currentRouteName() === 'hospital.getHospital') {
                    $rules = [
                    ];
                }
                if (Route::currentRouteName() === 'hospital.getHospitalById') {
                    $rules = [
                        "hospital_id" => [
                            "size:36"
                        ]
                    ];
                }
                if (Route::currentRouteName() === 'hospital.getHospitalAppend') {
                    $rules = [
                        "hospital_id" => [
                            "size:36"
                        ]
                    ];
                }
                if (Route::currentRouteName() === 'hospital.getHospitalFair') {
                    $rules = [
                        "hospital_id" => [
                            "required",
                            "size:36",
                            "exists:hospitals,hospital_id,deleted_at,NULL"
                        ]
                    ];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'hospital.addHospital') {
                    $rules = [
                        "hospital_id" => [
                            "required",
                            "size:36",
                        ],
                        "hospital_type" => [
                            //"required",
                            Rule::in(Config("const.HOSPITAL_TYPE"))
                        ]
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'hospital.updateHospitalById') {
                    $rules = [
                        "hospital_id" => [
                            "size:36",
                            "exists:hospitals,hospital_id,deleted_at,NULL"
                        ],
                        // "hospital_type" => [
                        //     Rule::in(config("const.HOSPITAL_TYPE"))
                        // ],
                    ];
                    $hospital_type = $this->request->get('hospital_type');
                    if (!empty($hospital_type)) {
                        $rules["hospital_type"] = [Rule::in(Config("const.HOSPITAL_TYPE"))];
                    }
                }
                break;
        }

        return $rules;
    }

    public function validationData()
    {
        return array_merge($this->all(),$this->route()->parameters());
    }
}
