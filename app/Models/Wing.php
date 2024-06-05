<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wing extends Model
{
    protected $connection='orcl_hrmis';
    protected $table = 'tbl_wing';
    protected $primaryKey = 'wing_id';

    protected $fillable=[
        'wing_head'
    ];
    public $timestamps = false;
}
