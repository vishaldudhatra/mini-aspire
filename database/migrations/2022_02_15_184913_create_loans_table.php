<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->decimal('approved_amount', 15, 2);
            $table->string('currency', 8)->default('USD');
            $table->tinyInteger('loan_terms')->comment('Unit: weekly');
            $table->decimal('interest_rate', 8, 4)->comment('Per weekly');
            $table->decimal('total_interest', 12, 2);
            $table->decimal('repayment_amount', 10, 2)->comment('Per weekly');
            $table->enum('status',['pending','approved','rejected','completed'])->default('approved');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
