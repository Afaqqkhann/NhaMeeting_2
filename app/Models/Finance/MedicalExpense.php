<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class MedicalExpense extends Model
{
    //protected $connection = 'sqlsrv_fin';
    protected $table = 'tbl_fin_med_exp';
    protected $primaryKey = 'med_exp_id';

    //public $timestamps = false;
}
