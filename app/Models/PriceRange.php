<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class PriceRange extends Model
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    public function application(){
        return $this->hasOne(Application::class,'id','application_id');
        // $instance->getQuery()->where('estatus','=', 1)->orderBy('sorting','asc');
        // return $instance;
    }
}
