<?php


namespace App\Http\requests\v1\Calendar;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules()
    {
        return [

        ];
    }


    /**
     * @return bool
     */
    public function authorize()
    {
        if ($this->bearerToken()) {
            return true;
        }
        return false;
    }
}
