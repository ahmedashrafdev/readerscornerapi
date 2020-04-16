<?php

namespace App\Http\Controllers;

use App\Product;
use App\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{

	public function bestseller(){
     return $bestsellers = Product::where('bestseller' , true)->orderBy('id' , 'desc')->get();
	}
	public function arabicBestseller(){
        return $arabicBestsellers = Product::where('arabic_bestseller' , true)->orderBy('id' , 'desc')->get();

	}
	public function teensBestseller(){
		return $teensBestsellers = Product::where('teens_bestseller' , true)->orderBy('id' , 'desc')->get();
	}
	public function popular(){
        return $populars = Product::where('popular' , true)->orderBy('id'  , 'desc')->get();

	}
	public function sliders(){
		return $sliders = Slider::all();
	}
        

        
}
