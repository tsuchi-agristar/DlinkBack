<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

use Illuminate\Validation\Rule;

class NotificationBlockRequest extends FormRequest
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
                if (Route::currentRouteName() === 'notificationblock.deleteNotificationblockById') {
                    $rules = [
                        "organization_id" => [
                            "exists:notificationblocks,organization_id",
                        ],
                    ];
                }
                break;

            case 'GET':
                break;

            case 'POST':
                if (Route::currentRouteName() === 'notificationblock.createNotificationblock') {
                    $rules = [
                        "organization_id" => [
                            "required",
                            "uuid",
                            "unique:notificationblocks,organization_id",
                        ]
                    ];
                }
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
