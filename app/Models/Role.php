<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    protected $fillable = ['name','display_name','description','application'];
    protected $dates = ['created_at'];
  

    public function permissions(){
        return $this->belongsToMany('App\Models\Permission','permission_role','role_id','permission_id');
    }

    public function users(){
        return $this->belongsToMany('App\Models\User','role_user','role_id','user_id');
    }
}