<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    protected $fillable = ['name' , 'shipping'];

    public static function getShippingValue($city) 
    {
    	return self::where('name' , $city)->first()->shipping;
    }

	public function users()
	{
	  return $this->hasMany('User');
	}
    
    
}
