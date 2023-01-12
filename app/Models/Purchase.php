<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function application(){
        return $this->hasOne(Application::class,'id','app_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function package(){
        return $this->hasOne(PriceRange::class,'id','package_id');
    }
}
