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
            $this->validateWithJson($validator);
        }

        try {
            // CHECK LOAD EXISTS
            $user = Auth::user();
            $loan_exists = Loan::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();


            if (!$loan_exists) {
                $interest = config('custom.interest_rate');

                $total_interest = $this->approvedAmount * ($interest * $this->loanTerms / 100);
                $repayment_amount = ($this->approvedAmount + $total_interest) / $this->loanTerms;

                $data = array(
                    'approved_amount' => $request->approvedAmount,
                    'currency' => $request->currency,
                    'loan_terms' => $request->loanTerms,
                    'interest_rate' => $interest,
                    'total_interest' => $total_interest,
                    'total_interest' => $total_interest,
                    'repayment_amount' => $repayment_amount,
                    'status' => 'approved',
                );

                $loan = Loan::create($data);

                return $this->respondWithJson(__('custom.loan_approved_succ'), LoanResource::make($loan), config('custom.create_response'));
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
        $loans = Loan::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if(!empty($loans)){
            return $this->respondWithJson('', LoanResource::collection($loans), config('custom.success_response'));
        }
        else{
            return $this->respondWithJson('No loan found.', [], config('custom.bad_request_response'));
        }
    }
}
