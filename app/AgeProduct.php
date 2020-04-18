<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgeProduct extends Model
{
    //
    protected $table = 'age_product';

    protected $fillable = ['product_id', 'age_id'];
}
