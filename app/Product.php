<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\ProductFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
class Product extends Model
{

    
    
    //
    protected $guarded = [];

    /**
     * Searchable rules.
     *
     * @var array
     */
    public function getImageAttribute($value)
	{
	    
	    $path = Storage::disk('public')->exists($value);
        if ($path) {
            return asset('storage/' . $value);
        } else {
            return $value;
        }
	}
	public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
	public function getPriceAttribute()
    {
        return isset($this->attributes['price'] ) ? $this->attributes['price'] / 100 . ".00" : null;
    }


    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = $value * 100;
    }
	public function getDiscountAttribute()
    {
        return isset($this->attributes['discount'] ) ? $this->attributes['discount'] / 100 . ".00" : null;
    }
    public function reviews()
    {
        return $this->hasMany('App\Review');
    }
    public function author(){
	    return $this->belongsTo("App\Author");
	}
    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
    

    public function ages()
    {
        return $this->belongsToMany('App\Age');
    }

    public function presentPrice()
    {
        return isset($this->discount) && $this->discount !== null ? money_format('EGP %i' , $this->discount) : money_format('EGP %i', $this->price);
    }

    public function scopeMightAlsoLike($query)
    {
        return $query->where('slug', '!=', $this->slug)
                     ->where('language_id' , $this->language_id)
                     ->take(6);
    }
    public function scopeFilter(Builder $builder, $request)
    {
        return (new ProductFilter($request))->filter($builder);
    }

    public function stockLevel()
    {
        $breakpoint = 2;
        if ($this->qty >  $breakpoint ) {
            $stockLevel = '<div class="badge badge-success">In Stock</div>';
        } elseif ($this->qty <=  $breakpoint && $this->qty) {
            $stockLevel = '<div class="badge badge-warning">Low Stock</div>';
        } else {
            $stockLevel = '<div class="badge badge-danger">Not available</div>';
        }

        return $stockLevel;
    }
    
    

    // The way average rating is calculated (and stored) is by getting an average of all ratings, 
    // storing the calculated value in the rating_cache column (so that we don't have to do calculations later)
    // and incrementing the rating_count column by 1

    public function recalculateRating($rating)
    {
        $reviews = $this->reviews()->approved();
        $avgRating = $reviews->avg('rating');
        $this->rating_cache = round($avgRating,1);
        $this->rating_count = $reviews->count();
        $this->save();
    }
}
