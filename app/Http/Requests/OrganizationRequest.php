<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
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
                if (Route::currentRouteName() === 'organization.deleteOrganizationById') {
                    $rules = [
                        "organization_id" => [
                            "exists:organizations,organization_id,deleted_at,NULL",
                        ],
                    ];
                }
                break;

            case 'GET':
                if (Route::currentRouteName() === 'organization.getOrganizationById') {
                    $rules = [
                        "organization_id" => [
                            // "exists:organizations,organization_id",
                        ],
                    ];
                }
                if (Route::currentRouteName() === 'organization.getUserByOrganizationId') {
                    $rules = [
                        "organization_id" => [
                            // "exists:organizations,organization_id",
                        ],
                    ];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'organization.addOrganization') {
                    $rules = [
                        "organization.organization_id" => [
                            "required",
                            "uuid",
                            "unique:organizations,organization_id",
                        ],
                        "organization.organization_type" => [
                            "required",
                            Rule::in(config('const.ORGANIZATION_TYPE'))
                        ],
                        "organization.organization_name" => [
                            "string",
                            "max:256",
                        ],
                        "organization.organization_name_kana" => [
                            "string",
                            "max:256",
                        ],
                        "organization.prefecture" => [
                            "string",
                            "max:32",
                        ],
                        "organization.city" => [
                            "string",
                            "max:32",
                        ],
                        "organization.address" => [
                            "string",
                            "max:128",
                        ],
                        "organization.homepage" => [
                            "nullable",
                            "url",
                            "max:256",
                        ],
                        "organization.dummy" => [
                            "boolean"
                        ],
                        "user.user_id" => "required|size:36", // 必須、文字数
                        "user.organization_id" => "required|size:36", // 必須、文字数
                        "user.mail_address" => "required|max:128", // 必須、最大文字数
                        "user.account_name" => "required|max:128|unique:users,account_name", // 必須、最大文字数、ユニーク
                        "user.password" => "required|max:128" // 必須、最大文字数
                    ];

                    $organization = $this->request->get('organization');
                    $organization_type = 0;
                    if (!empty($organization)) {
                        $organization_type = $organization["organization_type"];
                    }
                    if ($organization_type == config('const.ORGANIZATION_TYPE.HOSPITAL')) {
                        $rules["organization_sub.hospital_id"] = ["required","size:36"];
                        $organization_sub = $this->request->get('organization_sub');
                        if (!empty($organization_sub["hospital_type"])) {
                            $rules["organization_sub.hospital_type"] = [Rule::in(Config("const.HOSPITAL_TYPE"))];
                        }
                    }
                    else if ($organization_type == config('const.ORGANIZATION_TYPE.SCHOOL')) {
                        $rules["organization_sub.school_id"] = ["required","size:36"];
                    }
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'organization.updateOrganizationById') {
                    $rules = [
                        "organization_id" => [
                            "exists:organizations,organization_id,deleted_at,NULL",
                        ],
                        "organization_type" => [
                            Rule::in(config('const.ORGANIZATION_TYPE'))
                        ],
                        "organization_name" => [
                            "string",
                            "max:256",
                        ],
                        "organization_name_kana" => [
                            "string",
                            "max:256",
                        ],
                        "prefecture" => [
                            "string",
                            "max:32",
                        ],
                        "city" => [
                            "string",
                            "max:32",
                        ],
                        "address" => [
                            "string",
                            "max:128",
                        ],
                        "homepage" => [
                            "nullable",
                            "url",
                            "max:256",
                        ],
                        "dummy" => [
                            "boolean"
                        ]
                    ];
                }
                break;
        }
        return $rules;
    }

    public function validationData() {
        return array_merge($this->all(),$this->route()->parameters());
    }
}
