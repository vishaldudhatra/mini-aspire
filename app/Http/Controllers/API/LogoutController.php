<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Hash;


class LogoutController extends Controller
{
    public function index(Request $request){
        try {

            Auth::user()->tokens->each(function($token, $key) {
                $token->revoke();
                $token->delete();
            });
            return $this->respondWithJson(__('custom.scc_logout'), [], config('custom.create_response'));
        }
        catch (\Exception $exception) {
            return $this->respondWithJson($exception->getMessage(), [], config('custom.bad_request_response'));
        }
    }
}
