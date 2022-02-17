<?php

namespace App\Resources;

use App\Resources\RepaymentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'loanId' => (int) $this->id,
            'approvedAmount' => (float)$this->approved_amount,
            'currency' => (string)$this->currency,
            'loanTerms' => (int)$this->loan_terms,
            'interestRate' => (float)$this->interest_rate,
            'totalInterest' => (float)$this->total_interest,
            'repaymentAmount' => (float)$this->repayment_amount,
            'loanStatus' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'repayments' => RepaymentResource::collection($this->repayments),
        ];
    }
}
