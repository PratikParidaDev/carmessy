<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alert;
use App\Models\Make;
use App\Models\City;

class AlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $alerts = auth()->user()
            ->alerts()
            ->with(['make', 'model', 'city'])
            ->latest()
            ->paginate(20);

        return view('alerts.index', compact('alerts'));
    }

    public function create()
    {
        $makes = Make::where('is_active', true)->orderBy('name')->get();
        $cities = City::orderBy('name')->get();

        return view('alerts.create', compact('makes', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'make_id' => 'nullable|exists:makes,id',
            'model_id' => 'nullable|exists:models,id',
            'city_id' => 'nullable|exists:cities,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'min_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'max_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1) . '|gte:min_year',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,cng,lpg',
            'transmission' => 'nullable|in:manual,automatic,semi-automatic',
        ]);

        auth()->user()->alerts()->create($validated);

        return redirect()->route('alerts.index')
            ->with('success', 'Alert created successfully');
    }

    public function destroy(Alert $alert)
    {
        $this->authorize('delete', $alert);

        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', 'Alert deleted successfully');
    }

    public function toggle(Alert $alert)
    {
        $this->authorize('update', $alert);

        $alert->update(['is_active' => !$alert->is_active]);

        return redirect()->back()
            ->with('success', 'Alert ' . ($alert->is_active ? 'activated' : 'deactivated'));
    }

}
