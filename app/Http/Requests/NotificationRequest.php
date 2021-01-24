<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

use Illuminate\Validation\Rule;

class NotificationRequest extends FormRequest
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
                if (Route::currentRouteName() === 'notification.getNotificationsByOrganizationId') {
                    $rules = [];
                }
                if (Route::currentRouteName() === 'notification.getNotification') {
                    $rules = [];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'notification.createNotification') {
                    $rules = [
                        "notification_id" => [
                            "required",
                            "uuid"
                        ],
                        "notification_type" => [
                            "required",
                            Rule::in(config('const.NOTIFICATION_TYPE')),
                        ],
                        "operation_id" => [
                            "required",
                            "uuid"
                        ],
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'notification.updateNotification') {
                    $rules = [
                        "organization_id" => [
                            "required",
                            "uuid"
                        ],
                        "notification_id" => [
                            "required",
                            "uuid"
                        ],
                    ];
                }
                if (Route::currentRouteName() === 'notification.updateNotificationOfOrganization') {
                    $rules = [];
                }
                break;
        }
        return $rules;
    }

    public function validationData() {
        return array_merge($this->all(),$this->route()->parameters());
    }
}
