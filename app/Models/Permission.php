<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustPermission;

class Permission extends Model
{
    protected $fillable = ['name','display_name','description'];
    protected $dates = ['created_at'];
    
    public function roles(){
        return $this->belongsToMany('App\Models\Role','permission_role','permission_id','role_id');
    }
}
