<?php


namespace App\Http\requests\v1\Calendar;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest
{

    /**
     * @return string[]
     */
    public function rules()
    {
        return [
            'date'=>'date_format:Y-m-d H:i|required',
            'duration'=>'int|required',
            'title'=>'string|max:100|required'
        ];
    }


    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function check()
    {
            return true;
    }

}
