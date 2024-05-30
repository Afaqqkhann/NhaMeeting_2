<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class AdvAccounts extends Model
{
	//
	protected $connection = 'sqlsrv_fin_heads';
	protected $table = 'chartofaccountALL';
	protected $primaryKey = 'coaid';

	public $timestamps = false;
}
