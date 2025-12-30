<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function show(Dealer $dealer)
    {
        $dealer->load(['city', 'user']);
        
        $cars = $dealer->cars()
            ->approved()
            ->published()
            ->with(['make', 'model', 'city'])
            ->latest()
            ->paginate(12);

        return view('dealers.show', compact('dealer', 'cars'));
    }
}

