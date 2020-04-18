<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //
    protected $fillable = ['user_id' , 'product_id' , 'rating' , 'review' , 'approved'];

   

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    // Scopes
    public function scopeApproved($query)
    {
       	return $query->where('approved', true);
    }

    

    // Attribute presenters
    public function getTimeagoAttribute()
    {
    	$date = \Carbon\Carbon::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
    	return $date;
    }

    // this function takes in product ID, comment and the rating and attaches the review to the product by its ID, then the average rating for the product is recalculated
    public function storeReviewForProduct($productID, $review, $rating)
    {
        $product = Product::find($productID);

        //$this->user_id = Auth::user()->id;
        $this->review = $review;
        $this->user_id = auth()->user()->id;
        $this->rating = $rating;
        $product->reviews()->save($this);

        // recalculate ratings for the specified product
        $product->recalculateRating($rating);
    }
}
