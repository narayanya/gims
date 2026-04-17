<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\CoreCityVillage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationMasterController extends Controller
{
    // ── Country ──────────────────────────────────────────────
    public function countries(Request $request)
    {
        $query = Country::query();
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('country_name', 'like', '%' . $request->search . '%')
                ->orWhere('country_code', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        $countries = $query->orderBy('country_name')->paginate(20)->withQueryString();
        return view('master.location.countries', compact('countries'));
    }

    // ── State ─────────────────────────────────────────────────
    public function states(Request $request)
    {
        $query = State::with('country');
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('state_name', 'like', '%' . $request->search . '%')
                ->orWhere('state_code', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->country_id != '') {
            $query->where('country_id', $request->country_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        $states    = $query->orderBy('state_name')->paginate(20)->withQueryString();
        $countries = Country::orderBy('country_name')->get();
        return view('master.location.states', compact('states', 'countries'));
    }

    // ── District ──────────────────────────────────────────────
    public function districts(Request $request)
    {
        $query = District::with('state');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('district_name', 'like', '%' . $request->search . '%')
                ->orWhere('district_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        $districts = $query->orderBy('district_name')->paginate(20)->withQueryString();
        $states    = State::orderBy('state_name')->get();
        return view('master.location.districts', compact('districts', 'states'));
    }

    // ── City / Village ────────────────────────────────────────
    public function cities(Request $request)
    {
        $query = CoreCityVillage::with(['state', 'district']);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('city_village_name', 'like', '%' . $request->search . '%')
                ->orWhere('city_village_code', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        $cities    = $query->orderBy('city_village_name')->paginate(20)->withQueryString();
        $states    = State::orderBy('state_name')->get();
        $districts = District::orderBy('district_name')->get();
        return view('master.location.cities', compact('cities', 'states', 'districts'));
    }

    // ── Sync Countries from API ───────────────────────────────
    public function syncCountries(Request $request)
    {
        $apiKey  = env('LOCATION_API_KEY', 'TYbdjkjOwt5DPiiikuhy');
        $baseUrl = env('LOCATION_API_BASE_URL', 'https://api.countrystatecity.in/v1');

        try {
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $apiKey,
            ])->timeout(30)->get("{$baseUrl}/countries");

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed: HTTP ' . $response->status(),
                ], 422);
            }

            $data    = $response->json();
            $synced  = 0;
            $skipped = 0;

            foreach ($data as $item) {
                $countryName = $item['name'] ?? null;
                if (!$countryName) { $skipped++; continue; }

                Country::updateOrCreate(
                    ['country_code' => $item['iso2'] ?? $countryName],
                    [
                        'country_name'  => $countryName,
                        'country_code'  => $item['iso2']   ?? null,
                        'global_region' => $item['region'] ?? ($item['subregion'] ?? null),
                        'is_active'     => 1,
                    ]
                );
                $synced++;
            }

            return response()->json([
                'success' => true,
                'message' => "Sync complete. {$synced} countries synced, {$skipped} skipped.",
                'synced'  => $synced,
                'skipped' => $skipped,
                'total'   => Country::count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Country Sync Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
