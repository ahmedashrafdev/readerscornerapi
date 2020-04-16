<?php

// TypeFilter.php

namespace App\Filters;

class PriceFilter
{
    public function filter($builder, $value)
    {
        if ($value == '100_200') {
            return $builder->where('price' , '>=' , '10000')->where('price' , '<' , '20000');
            
        }
        if ($value == '20') {
            return $builder->where('price' , '<=' , '2000');
            
        }
        if ($value == '200_300') {
            return $builder->where('price' , '>=' , '20000')->where('price' , '<' , '30000');
            
        }
        if ($value == '300_400') {
            return $builder->where('price' , '>=' , '30000')->where('price' , '<' , '40000');
            
        }
        if ($value == '400_500') {
            return $builder->where('price' , '>=' , '40000')->where('price' , '<' , '50000');
            
        }
        if ($value == 'over_500') {
            return $builder->where('price' , '>=' , '50000');
            
        }
        if ($value == 'under_100') {
            return $builder->where('price' , '<=' , '10000');
            
        }
        
    }
}