<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'tbl_meeting_agenda';
    protected $primaryKey = 'ma_id';
    protected $fillable=[
        'ma_title',
        'ma_status',
        'ma_edoc',
        'ma_upload_date',
        'meeting_id',
        'action_id'
    ];

    public $timestamps = false;

    public function meeting()
    {
        return $this->belongsTo('App\Models\Meeting', 'meeting_id', 'meeting_id');
    }

    public function wing()
    {
        return $this->belongsTo('App\Models\Wing', 'action_id', 'wing_id');
    }
}
