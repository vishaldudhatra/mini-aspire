<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // VALDIATION RESPONSE
    protected function validateWithJson($validator) {
        return response()->json([
            'message'=>implode(" \n ",$validator->errors()->all())
        ], config('custom.bad_request_response'));
    }

    // SUCCESS RESPONSE
    protected function respondWithJson($message='',$data=array(), $code) {

        // ADD MESSAGE
        if($message != ''){
            $return['message'] = $message;
            if($data)
                $return['content'] = $data;
        }
        else{
            $return = $data;
        }

        return response()->json($return, $code);
    }
}
