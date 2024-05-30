<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wing extends Model
{
    protected $table = 'tbl_action';
    protected $primaryKey = 'action_id';

    protected $fillable=[
        'action_title',
        'action_status'
    ];
    public $timestamps = false;
}
