<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class EventMemberRequest extends FormRequest
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
                if (Route::currentRouteName() === 'eventMember.getChannelbyMemberId') {
                    $rules = [
                    ];
                }

                else if (Route::currentRouteName() === 'eventMember.getEventMemberById') {
                    $rules = [
                    ];
                }
                break;
            case 'PUT':
                if (Route::currentRouteName() === 'eventMember.updateEventMember') {
                    $rules = [
                        "*.member_role" => [
                            "nullable",
                            Rule::in(config("const.MEMBER_ROLE"))
                        ],
                    ];
                }
                break;
        }
        return $rules;
    }
}
