<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
class OnlineEventRequest extends FormRequest
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
                if (Route::currentRouteName() === 'onlineEvent.deleteOnlineEventById') {
                    $rules = [
                        "event_id" => [
                            "size:36"
                        ]
                    ];
                }
                break;

            case 'GET':
                if (Route::currentRouteName() === 'onlineEvent.getOnlineEvent') {
                    $rules = [
                    ];
                }
                else if (Route::currentRouteName() === 'onlineEvent.getOnlineEventById') {
                    $rules = [
                        "event_id" => [
                            "size:36"
                        ]
                    ];
                }
                else if (Route::currentRouteName() === 'onlineEvent.getEstimateOfEvent') {
                    $rules = [
                        "event_id" => [
                            "size:36"
                        ]
                    ];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'onlineEvent.createOnlineEvent') {
                    $rules = [
                        "event_id" => [
                            "required",
                            "size:36",
                        ],
                        "fair_id" => [
                            "size:36",
                            "exists:Fairs,fair_id,deleted_at,NULL"
                        ],
                        "event_type" => [
                            "required",
                            Rule::in(Config("const.EVENT_TYPE")),
                        ],
                        "event_status" => [
                            Rule::in(Config("const.EVENT_STATUS"))
                        ],
                        "channel_status" => [
                            Rule::in(Config("const.CHANNEL_STATUS"))
                        ],
                        "start_at" => [
                            "date_format:Y-m-d H:i:s",
                        ],
                        "end_at" => [
                            "date_format:Y-m-d H:i:s",
                        ]
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'onlineEvent.updateOnlineEventById') {
                    $rules = [
                        "event_id" => [
                            "required",
                            "size:36",
                            "exists:OnlineEvents,event_id,deleted_at,NULL"
                        ],
                        "fair_id" => [
                            "size:36",
                            "exists:Fairs,fair_id,deleted_at,NULL"
                        ],
                        "event_type" => [
                            Rule::in(Config("const.EVENT_TYPE")),
                        ],
                        "event_status" => [
                            Rule::in(Config("const.EVENT_STATUS"))
                        ],
                        "channel_status" => [
                            Rule::in(Config("const.CHANNEL_STATUS"))
                        ],
                        "start_at" => [
                            "date_format:Y-m-d H:i:s",
                        ],
                        "end_at" => [
                            "date_format:Y-m-d H:i:s",
                        ]
                    ];
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
