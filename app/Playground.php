<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playground extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'price',
        'address',
        'area',
        'imageURL',
        'avaiableFrom',
        'avaiableTo',
    ];


    public function booking(){
        return $this->belongsTo('App\Booking');
    }
    
}
