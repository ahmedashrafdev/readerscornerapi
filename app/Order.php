<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id', 'billing_email', 'billing_fname','billing_lname', 'billing_address', 'billing_city',
        'billing_postalcode', 'billing_phone', 'billing_discount', 'billing_discount_code', 'billing_subtotal',
        'billing_tax', 'billing_shipping', 'billing_total', 'payment_gateway', 'shipped','error'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getBillingTotalAttribute()
    {
        return isset($this->attributes['billing_total']) ? $this->attributes['billing_total'] / 100 . ".00" : null;
    }
    
    
    public function getBillingSubtotalAttribute()
    {
        return isset($this->attributes['billing_subtotal']) ? $this->attributes['billing_subtotal'] / 100 . ".00" : null;
    }

    public function products()
    {
        return $this->belongsToMany('App\Product')->withPivot('quantity');
    }
}
