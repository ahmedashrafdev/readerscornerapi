<?php

// CategoryFilter.php

namespace App\Filters;

class CategoryFilter
{
    public function filter($builder, $value)
    {
            return $builder->with('categories')->whereHas('categories', function ($query) use ($value) {
                $query->where('slug', request()->category);
            });
    }
}