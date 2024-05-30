<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class EmpAdvances extends Model
{
	//
	protected $connection = 'sqlsrv_fin';
	protected $table = 'V_EMP_ADV';
	protected $primaryKey = 'cnic';

	public $timestamps = false;

	public function accountTitle()
	{
		return $this->belongsTo('App\Models\Finance\AdvAccounts', 'AccountCode', 'AccountCode');
	}
}
