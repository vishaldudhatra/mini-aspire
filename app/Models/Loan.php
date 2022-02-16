<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'approved_amount',
        'currency',
        'loan_terms',
        'interest_rate',
        'total_interest',
        'repayment_amount',
        'status',
    ];


    public function repayments() {
        return $this->hasMany(Repayment::class);
    }
}
