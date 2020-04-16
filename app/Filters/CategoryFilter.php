<?php

// TypeFilter.php

namespace App\Filters;

class CategoryFilter
{
    public function filter($builder, $value)
    {
        return $builder->with('categories')->whereHas('categories', function ($query)  use ($value){
                $query->where('slug', $value);
            });
        /*if($value = 'all'){
            $products =  $builder->with('categories')->whereHas('categories', function ($query)  use ($value){
                $query->where('slug', 'gifts-and-stationary');
            });
            $stationary = $builder->with('categories')->whereHas('categories', function ($query)  use ($value){
                $query->where('slug', 'gifts-and-stationary');
            });
            $final = array_diff($products , $stationary);
            return $final;
        }else{
            return $builder->with('categories')->whereHas('categories', function ($query)  use ($value){
                $query->where('slug', $value);
            });
        }*/
            
    }
}