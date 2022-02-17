<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Resources\LoanResource;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Validator;

class LoanController extends Controller
{
    public function index(Request $request)  {

        $user = Auth::user();
        $loans = Loan::where('user_id', $user->id)->get();

        return $this->respondWithJson('', LoanResource::collection($loans), config('custom.success_response'));
    }

    public function store(Request $request) {
        $p = 10000;
        $r = 6;
        $n = 52;


        // VALIDATION RULE
        $validator = Validator::make($request->all(), [
            'approvedAmount' => 'required|integer',
            'loanTerms' => 'required|integer', // WEEKLY
            'currency' => 'required|string',
        ]);
        // VALIDATION MESSAGE
        $validation_field_name = array(
            'approvedAmount' =>  __('custom.approvedAmount'),
            'loanTerms' =>  __('custom.loanTerms'),
            'currency' =>  __('custom.currency'),
        );
        $validator->setAttributeNames($validation_field_name);

        // CHECK VALIDATION
        if ($validator->fails()) {
            return $this->validateWithJson($validator);
        }

        try {
            // CHECK LOAD EXISTS
            $user = Auth::user();
            $loan_exists = Loan::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();


            if (!$loan_exists) {
                $interest = (config('custom.interest_rate') / 100 / 52);
                //$total_interest = $request->approvedAmount * (($interest * $request->loanTerms) / 100);
                $total_interest = $request->approvedAmount  * $interest  * $request->loanTerms;
                $repayment_amount = ($request->approvedAmount + $total_interest) / $request->loanTerms;

                $data = array(
                    'approved_amount' => $request->approvedAmount,
                    'currency' => $request->currency,
                    'loan_terms' => $request->loanTerms,
                    'interest_rate' => round($interest,4),
                    'total_interest' => round($total_interest,2),
                    'repayment_amount' => round($repayment_amount,2),
                    'status' => 'approved',
                    'user_id' => Auth::user()->id,
                );

                $loan = Loan::create($data);

                return $this->respondWithJson(__('custom.loan_approved_succ'), new LoanResource($loan), config('custom.create_response'));
            }
            else{
                return $this->respondWithJson(__('custom.loan_request_exist'), [], config('custom.bad_request_response'));
            }
        }
        catch (\Exception $e) {
            return $this->respondWithJson($e->getMessage(), [], config('custom.bad_request_response'));
        }
    }

    public function show($id) {
        $user = Auth::user();
        $loan = Loan::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if(!empty($loan)){
            return $this->respondWithJson('', new LoanResource($loan), config('custom.success_response'));
        }
        else{
            return $this->respondWithJson('No loan found.', [], config('custom.bad_request_response'));
        }
    }
}
