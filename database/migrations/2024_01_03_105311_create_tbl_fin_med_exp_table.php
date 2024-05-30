<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTblFinMedExpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_fin_med_exp', function (Blueprint $table) {
            $table->integer('med_exp_id');
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->string('title', 200)->nullable();
            $table->string('emp_name', 150);
            $table->string('designation', 200)->nullable();
            $table->string('account_no', 100)->nullable();
            $table->string('coa', 100)->nullable();
            $table->integer('amount')->nullable();
            $table->integer('allowance')->nullable();
            $table->string('cnic', 30)->nullable();
            $table->string('region', 80)->nullable();
            $table->string('fin_year', 20)->nullable();
            $table->dateTime('cheque_date')->nullable();
            $table->string('account_code', 12)->nullable();
            $table->char('med_exp_status', 1)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
