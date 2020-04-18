<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = "category";


    protected $fillable = ['name' , 'slug' , 'image' , 'parent_id'];

    public function products()
    {
        return $this->belongsToMany('App\Product');
    }
    
    public function children() {
        return $this->HasMany('App\Category' , 'parent_id');
    }
    public function parent() {
        return $this->belongsTo('App\Category' , 'parent_id');
    }

}
