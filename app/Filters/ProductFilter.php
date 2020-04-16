<?php

// ProductFilter.php

namespace App\Filters;

use App\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends AbstractFilter
{
    protected $filters = [
        'category' => CategoryFilter::class,
        'age' => AgeFilter::class,
        'language' => LanguageFilter::class,
        'range' => PriceFilter::class,
        'author' => AuthorFilter::class,
        'price' => PriceFilter::class,
        'allBooks' => AllBooksFilter::class,
    ];
}