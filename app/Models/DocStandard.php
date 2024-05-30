<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocStandard extends Model
{
    protected $table = 'tbl_doc_standard';
    protected $primaryKey = 'doc_id';

    protected $fillable=[
        'doc_title',
        'doc_status'
    ];
    public $timestamps = false;
}
