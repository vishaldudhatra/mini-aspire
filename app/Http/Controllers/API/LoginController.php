<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    public function index(Request $request){
        // VALIDATION RULE
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // VALIDATION MESSAGE
        $validation_field_name = array(
            'email' =>  __('custom.email'),
            'password' =>  __('custom.password'),
        );
        $validator->setAttributeNames($validation_field_name);

        // CHECK VALIDATION
        if ($validator->fails()) {
            $this->validateWithJson($validator);
        }

        try {

            $user = User::where('email','=',$request->email)
                ->orderBy('id','DESC')
                ->first();

            if(!empty($user)){
                if (Hash::check($request->password, $user->password)){
                    $token = $user->createToken(@$user->id)->accessToken;

                    // RETURN RESPONSE

                    $return = UserResource::make($user)->resource;
                    $return->token = $token;

                    return $this->respondWithJson(__('custom.login_success'), $return, config('custom.create_response'));
                }
                else{
                    // PASSWORD MISMATCH
                    return $this->respondWithJson(__('custom.incorrect_password'), [], config('custom.create_response'));
                }
            }
            else{
                // ACCOUNT NOT EXISTS
                return $this->respondWithJson(__('custom.account_not_exists'), [], config('custom.not_found_response'));
            }
        }
        catch (\Exception $exception) {
            return $this->respondWithJson($exception->getMessage(), [], config('custom.bad_request_response'));
        }
    }
}
