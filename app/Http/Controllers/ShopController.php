<?php

namespace App\Http\Controllers;

use App\Age;
use App\Order;
use App\Review;
use App\Product;
use App\Category;
use App\Language;
use App\OrderProduct;
use GuzzleHttp\Client;
use App\Mail\OrderPlaced;
use Illuminate\Http\Request;
use App\Mail\OrderPlacedAdmin;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CheckoutRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ShopController extends Controller
{
    //
    public function categories($category = null){
        $categories = Category::where('parent_id', $category)->where('slug' , '!=' , 'bargain-shop')->where('slug' , '!=' , 'gifts-and-stationary')->where('slug' , '!=' , 'arabic-books')->get();
        $categories = count($categories) > 0 ? $categories : Category::where('parent_id', null)->where('slug' , '!=' , 'bargain-shop')->where('slug' , '!=' , 'gifts-and-stationary')->where('slug' , '!=' , 'arabic-books')->get();
        return $categories;
    }
    public function authors(){

    }
    public function languages(){
        $languages = Language::get();
        return $languages;
    }
    public function ages(){
        $ages = Age::get();
        return $ages;
    }
    public function products(Request $request){
        $request['pagination'] = $request['pagination'] == null ? 8 : $request['pagination'];
        $products = Product::select(['id' , 'name' , 'slug' , 'image' , 'price' , 'discount' , 'author_id' ,  'rating_cache'])->with('author')->filter($request)->orderBy('description', 'desc')->paginate($request['pagination']);
        
        return $products;
    }

    public function product($id){
        $product = Product::where('id', $id)->firstOrFail();
        $mightAlsoLike = $product->mightAlsoLike();
        $stockLevel = $product->stockLevel();
        $reviews = $product->reviews()->with('user')->approved()->orderBy('created_at','desc')->paginate(10);
        return ['product' => $product , 'mightAlsoLike' => $mightAlsoLike , 'stockLevel' => $stockLevel , 'reviews' => $reviews];
    }

    public function storeReview(Request $request)
    {
        $review = new Review;
        $review->storeReviewForProduct($request->productId, $request->review, $request->rating);
        return back()->with('success_message', 'Your Review Has Been Added Successfully!');
    }

    
   
}
