<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    //
    protected $table = "languages";


    protected $fillable = ['name' , 'slug'];

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }

}