<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'tbl_meeting';
    protected $primaryKey = 'meeting_id';

    protected $fillable=[
        'meeting_date',
        'meeting_no',
        'meeting_edoc',
        'meeting_type',
        'meeting_status',
        'meeting_upload_date'
    ];
    public $timestamps = false;

    public function meetingType()
    {
        return $this->belongsTo('App\Models\MeetingType', 'meeting_type', 'mt_id');
    }
    public function meetingagenda()
    {
        return $this->hasMany('App\Models\Agenda', 'ma_id', 'ma_id');
    }
    
}
