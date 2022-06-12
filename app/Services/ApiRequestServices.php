<?php

namespace App\Services;



class ApiRequestServices
{

    public function getError()
    {
        $error = [
            'error'=>[
                'message'=>'Request does not contain a date'
            ]
        ];
        $error = json_encode($error);
        return $error;
    }


}
