<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Age extends Model
{
    //
    protected $table = "ages";


    protected $fillable = ['name' , 'slug'];

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }

}
