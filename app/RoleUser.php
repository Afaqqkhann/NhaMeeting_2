<?php namespace App;

use App\User;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{

    public function user(){
        return $this->belongsToMany(User);
    }

}