<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UserRequest extends FormRequest
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
                if (Route::currentRouteName() === 'user.deleteUserById') {
                    $rules = [
                        'user_id' => 'required|size:36', // 必須、文字数
                    ];
                }
                break;
            
            case 'POST':
                if (Route::currentRouteName() === 'user.createUser') {
                    $rules = [
                        'user_id' => 'required|size:36', // 必須、文字数
                        'organization_id' => 'required|size:36|exists:Organizations,organization_id,deleted_at,NULL', // 必須、文字数、存在
                        'mail_address' => 'required|max:128', // 必須、最大文字数
                        'account_name' => 'required|max:128|unique:users,account_name', // 必須、最大文字数、ユニーク
                        'password' => 'required|max:128' // 必須、最大文字数
                    ];
                } else if (Route::currentRouteName() === 'user.login') {
                    $rules = [
                        'account' => 'required|max:128', // 必須、最大文字数
                        'password' => 'required|max:128' // 必須、最大文字数
                    ];
                }
                break;
            
            case 'PUT':
                if (Route::currentRouteName() === 'user.updateUserById') {
                    $rules = [
                        'user_id' => 'required|size:36|exists:Users,user_id,deleted_at,NULL', // 必須、文字数
                        'organization_id' => 'size:36', // 文字数
                        'mail_address' => 'max:128', // 最大文字数
                        'account_name' => 'max:128|unique:users,account_name,' . $this->request->all()['user_id']  . ',user_id', // 最大文字数、ユニーク(自身を除く)
                        'password' => 'max:128' // 最大文字数
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
            'user_id' => $this->user_id
        ]);
    }
}
