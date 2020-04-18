<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponUser extends Model
{
    //
     protected $fillable = ['user_id', 'coupon_id'];
    protected $table="coupon_user";
    public $timestamps = false;
}
