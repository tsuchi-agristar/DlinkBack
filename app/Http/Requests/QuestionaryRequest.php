<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class QuestionaryRequest extends FormRequest
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
                if (Route::currentRouteName() === 'questionary.getQuestionary') {
                    $rules = [];
                }
                break;
            
            case 'POST':
                if (Route::currentRouteName() === 'questionary.createQuestionary') {
                    $rules = [
                        'questionary_id' => 'required|size:36', // 必須、文字数
                        'school_id' => 'required|size:36' // 必須、文字数
                    ];
                }
                break;
        }
        return $rules;
    }
}
