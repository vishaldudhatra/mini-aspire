<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Resources\UserResource;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index(Request $request){
        // VALIDATION RULE
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|max:50',
            'lastName' => 'required|max:50',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|min:6',
        ]);
        // VALIDATION MESSAGE
        $validation_field_name = array(
            'firstName' =>  __('custom.firstName'),
            'lastName' =>  __('custom.lastName'),
            'email' =>  __('custom.email'),
            'password' =>  __('custom.password'),
        );
        $validator->setAttributeNames($validation_field_name);

        // CHECK VALIDATION
        if ($validator->fails()) {
            return $this->validateWithJson($validator);
        }
        else{
            try {
                // CREATE USER
                $data = [
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
                $user = User::create($data);

                $token = $user->createToken($user->id);

                // RETURN RESPONSE
                $return = UserResource::make($user)->resource;
                $return->accessToken = $token->accessToken;


                return $this->respondWithJson(__('custom.register_success'), [$return], config('custom.create_response'));
            } catch (\Exception $exception) {
                return $this->respondWithJson($exception->getMessage(), [], config('custom.bad_request_response'));
            }
        }
    }
}
