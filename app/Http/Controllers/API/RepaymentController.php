<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Repayment;
use App\Resources\RepaymentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class RepaymentController extends Controller
{
    public function index(Request $request){
        // VALIDATION RULE
        $validator = Validator::make($request->all(), [
            'amount' =>  ['required', 'regex:/^\d*(\.\d{2})?$/'],
            'repaymentMethod' => 'required|max:100',
        ]);
        // VALIDATION MESSAGE
        $validation_field_name = array(
            'amount' =>  __('custom.amount'),
            'repaymentMethod' =>  __('custom.repaymentMethod'),
        );
        $validator->setAttributeNames($validation_field_name);

        // CHECK VALIDATION
        if ($validator->fails()) {
            return $this->validateWithJson($validator);
        }

        try {
            $user = Auth::user();
            $loan = Loan::where('user_id', $user->id)
                ->where('status', 'approved')
                ->first();

            if(!empty($loan)){
                if($loan->repayment_amount == $request->amount){

                    // REPAYMNT
                    $repayment = $loan->repayments()->create([
                        'repayment_amount' => $request->amount,
                        'repayment_method' => $request->repaymentMethod,
                        'user_id' => Auth::user()->id,
                    ]);

                    // UPDATE LOAN STATUS
                    if($loan->repayments->count() == $loan->loan_terms){
                        $loan->status = 'completed';
                        $loan->save();
                    }

                    // SUCEESS
                    return $this->respondWithJson(__('custom.repayment_succ'),  RepaymentResource::make($repayment), config('custom.create_response'));
                }
                else{
                    // AMOUNT MISSMATHC
                    return $this->respondWithJson(__('custom.loan_amount_not_same'), [], config('custom.bad_request_response'));
                }
            }
            else{
                // LOAN NOT FOUND
                return $this->respondWithJson(__('custom.loan_not_eixst'), [], config('custom.not_found_response'));
            }
        }
        catch (\Exception $e) {
            return $this->respondWithJson($e->getMessage(), [], config('custom.bad_request_response'));
        }
    }
}
