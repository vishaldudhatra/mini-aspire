<?php

namespace App\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'repaymentId' => (int) $this->id,
            'loanId' => $this->loan_id,
            'repaymentAmount' => $this->repayment_amount,
            'repaymentMethod' => $this->repayment_method,
            'paid_at' => $this->created_at,
        ];
    }
}
