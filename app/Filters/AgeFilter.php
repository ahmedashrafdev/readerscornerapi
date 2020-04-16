<?php

// TypeFilter.php

namespace App\Filters;

class AgeFilter
{
    public function filter($builder, $value)
    {
            return $builder->with('ages')->whereHas('ages', function ($query)  use ($value){
                $query->where('slug', $value);
            });
    }
}