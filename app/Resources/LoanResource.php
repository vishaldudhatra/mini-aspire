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
            'approvedAmount' => $this->approved_amount,
            'currency' => $this->currency,
            'loanTerms' => $this->loan_terms,
            'interestRate' => $this->interest_rate,
            'totalInterest' => $this->total_interest,
            'repaymentAmount' => $this->repayment_amount,
            'loanStatus' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'repayments' => RepaymentResource::collection($this->whenLoaded('repayments')),
        ];
    }
}
