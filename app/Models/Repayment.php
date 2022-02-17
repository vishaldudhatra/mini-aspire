<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    use HasFactory;

    const UPDATED_AT = NULL;

    protected $fillable = [
        'user_id',
        'loan_id',
        'repayment_amount',
        'repayment_method',
        'paid_at',
    ];
}
