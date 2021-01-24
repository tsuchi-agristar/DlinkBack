<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class FairApplicationRequest extends FormRequest
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
                if (Route::currentRouteName() === 'fairApplication.deleteFairApplication') {
                    $rules = [
                        'application_id' => 'required|size:36' // 必須、文字数
                    ];
                }
                break;

            case 'GET':
                if (Route::currentRouteName() === 'fairApplication.getFairApplication') {
                    $rules = [];
                } else if (Route::currentRouteName() === 'fairApplication.getFairApplicationById') {
                    $rules = [];
                } else if (Route::currentRouteName() === 'fairApplication.getFairOfApplication') {
                    $rules = [];
                } else if (Route::currentRouteName() === 'fairApplication.getApplicationOfFairApplication') {
                    $rules = [];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'fairApplication.createFairApplication') {
                    $rules = [
                        'application_id' => 'required|size:36', // 必須、文字数
                        'fair_id' => 'required|size:36', // 必須、文字数
                        'school_id' => 'required|size:36|unique:fairapplications,school_id,NULL,fair_id,fair_id,' . $this->input('fair_id'), // 必須、文字数、ユニーク
                        // 'application_datetime' => 'required', // 必須
                        // 'application_status' => 'required', // 必須
                        'estimate_participant_number' => 'required', // 必須
                        'format' => 'required', // 必須
                        // 'comment' => 'required' // 必須
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'fairApplication.updateFairApplicationById') {
                    $rules = [
                        'application_id' => 'required|size:36|exists:FairApplications,application_id,deleted_at,NULL', // 必須、文字数、存在
                        'fair_id' => 'size:36', // 文字数
                        'school_id' => 'size:36', // 文字数
                        'application_datetime' => '', //
                        'application_status' => '', //
                        'estimate_participant_number' => '', //
                        'format' => '', //
                        'comment' => '' //
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
            'application_id' => $this->application_id
        ]);
    }
}
