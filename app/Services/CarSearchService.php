<?php

namespace App\Services;

use App\Models\Car;
use Illuminate\Http\Request;

class CarSearchService
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return Car::approved()
                ->published()
                ->with(['make', 'model', 'city', 'dealer'])
                ->latest('published_at')
                ->paginate(20);
        }

        // Use Laravel Scout for full-text search
        $searchResults = Car::search($query)
            ->query(fn ($builder) => $builder->with(['make', 'model', 'city', 'dealer']))
            ->paginate(20);

        return $searchResults;
    }

    public function advancedSearch(array $filters)
    {
        $query = Car::approved()->published()->with(['make', 'model', 'city', 'dealer']);

        if (!empty($filters['make_id'])) {
            $query->where('make_id', $filters['make_id']);
        }

        if (!empty($filters['model_id'])) {
            $query->where('model_id', $filters['model_id']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (!empty($filters['min_year'])) {
            $query->where('year', '>=', $filters['min_year']);
        }

        if (!empty($filters['max_year'])) {
            $query->where('year', '<=', $filters['max_year']);
        }

        if (!empty($filters['fuel_type'])) {
            $query->where('fuel_type', $filters['fuel_type']);
        }

        if (!empty($filters['transmission'])) {
            $query->where('transmission', $filters['transmission']);
        }

        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'year_new' => $query->orderBy('year', 'desc'),
            'year_old' => $query->orderBy('year', 'asc'),
            'mileage_low' => $query->orderBy('mileage', 'asc'),
            default => $query->latest('published_at'),
        };

        return $query->paginate(20);
    }
}




?>