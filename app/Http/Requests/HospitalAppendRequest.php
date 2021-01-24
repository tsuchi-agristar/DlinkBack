<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
class HospitalAppendRequest extends FormRequest
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
                if (Route::currentRouteName() === 'hospitalAppend.deleteHospitalAppendById') {
                    $rules = [
                    ];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'hospitalAppend.createHospitalAppend') {
                    $rules = [
                        // 付属情報のバリデーション
                        "*.append_information_id" => [
                            "required",
                            "size:36",
                        ],
                        "*.append_information_type" => [
                            "required",
                            "integer",
                            Rule::in(Config("const.APPEND_INFO_TYPE"))
                        ],
                        "*.hospital_id" => [
                            "required",
                            "size:36",
                            "exists:Hospitals,hospital_id,deleted_at,NULL"
                        ],
                        "*.recruiting_job_type" => [
                            "required",
                            "integer",
                            Rule::in(Config("const.JOB_TYPE")),
                        ],
                        "*.recruiting_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.recruiting_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        // インターンシップのバリデーション
                        "*.hospital_intership.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_intership.training_period_start" => [
                            "date_format:Y-m-d",
                        ],
                        "*.hospital_intership.training_period_end" => [
                            "date_format:Y-m-d",
                        ],
                        // スカラシップバリデーション
                        "*.hospital_scholarship.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_scholarship.loan_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.loan_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.payback_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.payback_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.payback_exemption" => [
                            "boolean"
                        ],
                        // 実習情報
                        "*.hospital_practice.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_practice.practice_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_practice.practice_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        // 病院説明会
                        "*.hospital_fair.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_fair.hospital_fair_type" => [
                            "integer",
                            Rule::in(Config("const.FAIR_TYPE"))
                        ],
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'hospitalAppend.updateHospitalAppend') {
                    $rules = [
                        // 付属情報テーブルのバリデーション
                        "*.append_information_id" => [
                            "required",
                            "size:36",
                            "exists:hospitalAppends,append_information_id,deleted_at,NULL"
                        ],
                        "*.append_information_type" => [
                            "required",
                            "integer",
                            Rule::in(Config("const.APPEND_INFO_TYPE")),
                        ],
                        "*.hospital_id" => [
                            "size:36",
                            "exists:Hospitals,hospital_id,deleted_at,NULL"
                        ],
                        "*.recruiting_job_type" => [
                            Rule::in(Config("const.JOB_TYPE"))
                        ],
                        "*.recruiting_period_start" => [
                            "date_format:Y-m-d",
                        ],
                        "*.recruiting_period_end" => [
                            "date_format:Y-m-d",
                        ],
                        // インターンシップのバリデーション
                        "*.hospital_intership.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_intership.training_period_start" => [
                            "date_format:Y-m-d",
                        ],
                        "*.hospital_intership.training_period_end" => [
                            "date_format:Y-m-d",
                        ],
                        // スカラシップバリデーション
                        "*.hospital_scholarship.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_scholarship.loan_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.loan_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.payback_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.payback_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_scholarship.payback_exemption" => [
                            "boolean"
                        ],
                        // 実習情報
                        "*.hospital_practice.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_practice.practice_period_start" => [
                            "date_format:Y-m-d"
                        ],
                        "*.hospital_practice.practice_period_end" => [
                            "date_format:Y-m-d"
                        ],
                        // 病院説明会
                        "*.hospital_fair.append_information_id" => [
                            "sometimes",
                            "required",
                            "size:36",
                        ],
                        "*.hospital_fair.hospital_fair_type" => [
                            "integer",
                            Rule::in(Config("const.FAIR_TYPE"))
                        ],
                    ];
                }
                break;
        }
        return $rules;
    }
}
