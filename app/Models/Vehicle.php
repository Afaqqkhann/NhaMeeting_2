<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{

    protected $table = 'TBL_VEHICLE';
    protected $primaryKey = 'vehid';
    public $timestamps = false;
}
