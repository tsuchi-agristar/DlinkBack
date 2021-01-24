<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class SchoolRequest extends FormRequest
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
                if (Route::currentRouteName() === 'school.deleteSchoolById') {
                    $rules = [
                        // 'school_id' => 'required|size:36|exists:Schools,school_id,deleted_at,NULL' // 必須、文字数
                    ];
                }
                break;

            case 'GET':
                if (Route::currentRouteName() === 'school.getSchool') {
                    $rules = [];
                } else if (Route::currentRouteName() === 'school.getSchoolById') {
                    $rules = [];
                }
                break;

            case 'POST':
                if (Route::currentRouteName() === 'school.addSchool') {
                    $rules = [
                        'school_id' => 'required|size:36', // 必須、文字数
                        'school_type' => '', //
                        'student_number' => '', //
                        'scholarship_request' => '', //
                        'internship_request' => '', //
                        'practice_request' => '' //
                    ];
                }
                break;

            case 'PUT':
                if (Route::currentRouteName() === 'school.updateSchoolById') {
                    $rules = [
                        'school_id' => 'required|size:36|exists:Schools,school_id,deleted_at,NULL', // 必須、文字数、存在
                        'school_type' => '', //
                        'student_number' => '', //
                        'scholarship_request' => '', //
                        'internship_request' => '', //
                        'practice_request' => '' //
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
            'school_id' => $this->school_id
        ]);
    }
}
