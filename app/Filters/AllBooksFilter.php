<?php

// TypeFilter.php

namespace App\Filters;

class AllBooksFilter
{
    public function filter($builder, $value)
    {
        return $builder->with('categories')->whereDoesntHave('categories', function ($query){
                $query->where('slug', 'gifts-and-stationary');
            })->whereDoesntHave('categories', function ($query){
                $query->where('slug', 'bargain-shop');
            });
    }
}