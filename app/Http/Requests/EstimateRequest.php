<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class EstimateRequest extends FormRequest
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
                if (Route::currentRouteName() === 'estimate.deleteEstimateById') {
                    $rules = [
                        'estimate_id' => 'required|size:36' // 必須、文字数
                    ];
                }
                break;
            
            case 'POST':
                if (Route::currentRouteName() === 'estimate.createEstimate') {
                    $rules = [
                        'estimate_id' => 'required|size:36', // 必須、文字数
                        'event_id' => 'required|size:36', // 必須、文字数
                        'estimate_status' => 'required', // 必須
                        'regular_price' => '', //
                        'discunt_price' => '', //
                        'estimate_price' => '' //
                    ];
                }
                break;
            
            case 'PUT':
                if (Route::currentRouteName() === 'estimate.updateEstimateById') {
                    $rules = [
                        'estimate_id' => 'required|size:36|exists:Estimates,estimate_id,deleted_at,NULL', // 必須、文字数
                        'event_id' => 'required|size:36', // 必須、文字数
                        'estimate_status' => 'required', // 必須
                        'regular_price' => '', //
                        'discunt_price' => '', //
                        'estimate_price' => '' //
                    ];
                }
                break;
        }
        return $rules;
    }

    /**
     * [Override] リクエストマージ
     *
     * {@inheritdoc}
     *
     * @see \Illuminate\Foundation\Http\FormRequest::validationData()
     */
    public function validationData()
    {
        return array_merge($this->request->all(), [
            'estimate_id' => $this->estimate_id
        ]);
    }
}
