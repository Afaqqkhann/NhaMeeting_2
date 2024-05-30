<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingDocument extends Model
{
    protected $table = 'tbl_meeting_doc';
    protected $primaryKey = 'md_id';
    protected $fillable=[
        'md_title',
        'md_status',
        'md_edoc',
        'md_upload_date',
        'meeting_id',
        'doc_id'
    ];

    public $timestamps = false;

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting', 'meeting_id', 'meeting_id');
    }

    public function doctsandard()
    {
        return $this->belongsTo('App\Models\DocStandard', 'doc_id', 'doc_id');
    }
}
