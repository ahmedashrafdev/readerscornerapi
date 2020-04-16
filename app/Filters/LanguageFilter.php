<?php

// TypeFilter.php

namespace App\Filters;

class LanguageFilter
{
    public function filter($builder, $value)
    {
        $languageId = \App\Language::where('slug' , $value)->first()->id;
            return $builder->where('language_id', $languageId);
    }
}