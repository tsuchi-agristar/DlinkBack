<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use App\Models\Hospital;
use App\Models\Fair;
class FairRequest extends FormRequest
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
                if (Route::currentRouteName() === 'fair.deleteFairById') {
                    $rules = [
                        "fair_id" => [
                            "size:36"
                        ]
                    ];
                }
                break;

            case 'GET':
                if (Route::currentRouteName() === 'fair.getFair') {
                    $rules = [
                    ];
                }
                else if (Route::currentRouteName() === 'fair.getFairById') {
                    $rules = [
                        "fair_id" => [
                            "size:36"
                        ]
                    ];
                }
                else if (Route::currentRouteName() === 'fair.getApplicationOfFair') {
                    $rules = [
                        "fair_id" => [
                            "size:36"
                        ]
                    ];
                }
                else if (Route::currentRouteName() === 'fair.getOnlineEventOfFair') {
                    $rules = [
                        "fair_id" => [
                            "size:36"
                        ]
                    ];
                }
                else if (Route::currentRouteName() === 'fair.getDetailOfFair') {
                    $rules = [
                        "fair_id" => [
                            "size:36"
                        ]
                    ];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'fair.addFair') {
                    $rules = [
                        "fair_id" => [
                            "size:36",
                        ],
                        "hospital_id" => [
                            "size:36",
                            "exists:hospitals,hospital_id,deleted_at,NULL"
                        ],
                        "fair_status" => [
                            Rule::in(config('const.FAIR_STATUS'))
                        ],
                        "plan_start_at" => [
                            "date_format:Y-m-d"
                        ],
                        "plan_end_at" => [
                            "date_format:Y-m-d"
                        ],
                        // 説明会_説明会種別
                        "fair_type.*.fair_id" => [
                            "size:36"
                        ],
                        "fair_type.*.fair_type" => [
                            Rule::in(config("const.FAIR_TYPE"))
                        ],
                        // 付属情報種別のチェック
                        "append.*.append_information_type" => [
                            Rule::in(config("const.APPEND_INFO_TYPE"))
                        ],
                        "append.*.hospital_id" => [
                            "size:36",
                            "exists:hospitals,hospital_id,deleted_at,NULL"
                        ],
                        "append.*.recruiting_job_type" => [
                            "nullable",
                            Rule::in(config("const.JOB_TYPE"))
                        ],
                        "append.*.recruiting_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.recruiting_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        // インターンシップ情報のチェック
                        "append.*.hospital_intership.training_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_intership.training_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        // スカラシップ情報のチェック
                        "append.*.hospital_scholarship.loan_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.loan_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.payback_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.payback_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.payback_exemption" => [
                            "nullable",
                            // "boolean",
                        ],
                        // 実習情報
                        "append.*.hospital_practice.practice_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_practice.practice_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        // 説明会
                        "append.*.hospital_fair.hospital_fair_type.*.hospital_fair_type" => [
                            "nullable",
                            Rule::in(config("const.FAIR_TYPE"))
                        ],
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'fair.updateFairById') {
                    $rules = [
                        "fair_id" => [
                            "size:36",
                            "exists:fairs,fair_id,deleted_at,NULL",
                        ],
                        "hospital_id" => [
                            "size:36",
                            "exists:hospitals,hospital_id,deleted_at,NULL",
                        ],
                        "fair_status" => [
                            Rule::in(config("const.FAIR_STATUS"))
                        ],
                        "plan_start_at" => [
                            "date_format:Y-m-d"
                        ],
                        "plan_end_at" => [
                            "date_format:Y-m-d"
                        ],
                        "fair_type.*.fair_id" => [
                            "size:36"
                        ],
                        "fair_type.*.fair_type" => [
                            Rule::in(config("const.FAIR_TYPE"))
                        ],
                        // 付属情報種別のチェック
                        "append.*.append_information_type" => [
                            Rule::in(config("const.APPEND_INFO_TYPE"))
                        ],
                        "append.*.hospital_id" => [
                            "size:36",
                            "exists:hospitals,hospital_id,deleted_at,NULL"
                        ],
                        "append.*.recruiting_job_type" => [
                            "nullable",
                            Rule::in(config("const.JOB_TYPE"))
                        ],
                        "append.*.recruiting_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.recruiting_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],

                        // インターンシップ情報のチェック
                        "append.*.hospital_intership.training_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_intership.training_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],

                        // スカラシップ情報のチェック
                        "append.*.hospital_scholarship.loan_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.loan_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.payback_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.payback_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_scholarship.payback_exemption" => [
                            "nullable",
                            // "boolean",
                        ],

                        // 実習情報
                        "append.*.hospital_practice.practice_period_start" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],
                        "append.*.hospital_practice.practice_period_end" => [
                            "nullable",
                            "date_format:Y-m-d"
                        ],

                        // 説明会
                        "append.*.hospital_fair.hospital_fair_type.*.hospital_fair_type" => [
                            "nullable",
                            Rule::in(config("const.FAIR_TYPE"))
                        ],
                    ];
                }
                break;
        }
        return $rules;
    }

    // override
    public function validationData()
    {
        return array_merge($this->all(),$this->route()->parameters());
    }
}
