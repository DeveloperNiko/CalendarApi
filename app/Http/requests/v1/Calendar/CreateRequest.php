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
            'date_start'=>'date_format:Y-m-d H:i|required',
            'date_end'=>'date_format:Y-m-d H:i|required',
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
        if (Auth::check()) {
            return true;
        }
        return false;
    }

}
