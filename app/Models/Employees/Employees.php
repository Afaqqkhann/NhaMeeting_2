<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    //
	protected $table = 'TBL_EMP';
	protected $primaryKey = 'emp_id';
	
	public $timestamps = false;
	
	public function arc() {
		return $this->hasMany('App\Models\ACR');
	}
}
