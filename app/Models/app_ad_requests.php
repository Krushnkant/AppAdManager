<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class app_ad_requests extends Model
{
    use HasFactory;

    public function ad_request_status(){
        return $this->hasMany(ad_request_status::class,'app_request_id','id');
        // $instance->getQuery()->where('estatus','=', 1)->orderBy('sorting','asc');
        // return $instance;
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
        // $instance->getQuery()->where('estatus','=', 1)->orderBy('sorting','asc');
        // return $instance;
    }
}
