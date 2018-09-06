<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //
    protected $fillable = [
        'user_id',
        'playground_id',
        'bookedDateFrom',
        'bookedDateTo',
        'bookedTimeFrom',
        'bookedTimeTo',
        'approved',
        'price',
    ];
    
    
    public function user(){
        return $this->hasMany('App\User');
    }

    public function playground(){
        return $this->hasMany('App\Playground');
    }

}
