<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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
                if (Route::currentRouteName() === 'payment.getPayment') {
                    $rules = [
                    ];
                }
                else if (Route::currentRouteName() === 'payment.getpaymentById') {
                    $rules = [
                    ];
                }
                break;
            case 'PUT':
                if (Route::currentRouteName() === 'payment.updatePaymentById') {
                    $rules = [
                        "payment_id" => [
                            "size:36",
                        ],
                        "payment_hospital_id" => [
                            "exists:hospitals,hospital_id,deleted_at,NULL",
                        ],
                        "payment_month" => [
                            "nullable",
                            "date_format:Y-m-d",
                        ],
                        "payment_status" => [
                            "nullable",
                            Rule::in(config('const.PAYMENT_STATUS')),
                        ],
                        "payment_price" => [
                            "nullable",
                            "numeric",
                        ]
                    ];
                }
                break;
        }
        return $rules;
    }
}
