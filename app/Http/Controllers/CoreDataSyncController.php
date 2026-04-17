<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Crop;
use App\Models\Variety;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\CropCategory;
use App\Models\CropType;
use App\Models\VarietyType;
use App\Models\Season;
use App\Models\SeedClass;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class CoreDataSyncController extends Controller
{
    public function index()
    {
        return view('sync.index');
    }

    public function syncData(Request $request)
    {
        $request->validate([
            'sync_type' => 'required|string',
            'api_url' => 'required|url',
            'api_token' => 'nullable|string',
        ]);

        $syncType = $request->sync_type;
        $apiUrl = $request->api_url;
        $apiToken = $request->api_token;

        try {
            $headers = [];
            if ($apiToken) {
                $headers['Authorization'] = 'Bearer ' . $apiToken;
            }

            $response = Http::withHeaders($headers)->get($apiUrl);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API request failed: ' . $response->status()
                ], 400);
            }

            $data = $response->json();
            $synced = 0;
            $errors = [];

            switch ($syncType) {
                case 'crops':
                    $synced = $this->syncCrops($data);
                    break;
                case 'varieties':
                    $synced = $this->syncVarieties($data);
                    break;
                case 'categories':
                    $synced = $this->syncCategories($data);
                    break;
                case 'units':
                    $synced = $this->syncUnits($data);
                    break;
                case 'warehouses':
                    $synced = $this->syncWarehouses($data);
                    break;
                case 'crop_categories':
                    $synced = $this->syncCropCategories($data);
                    break;
                case 'crop_types':
                    $synced = $this->syncCropTypes($data);
                    break;
                case 'variety_types':
                    $synced = $this->syncVarietyTypes($data);
                    break;
                case 'seasons':
                    $synced = $this->syncSeasons($data);
                    break;
                case 'seed_classes':
                    $synced = $this->syncSeedClasses($data);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid sync type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$synced} records",
                'synced_count' => $synced
            ]);

        } catch (\Exception $e) {
            Log::error('CoreData Sync Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function syncCrops($data)
    {
        $count = 0;
        foreach ($data as $item) {
            Crop::updateOrCreate(
                ['name' => $item['name']],
                [
                    'scientific_name' => $item['scientific_name'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncVarieties($data)
    {
        $count = 0;
        foreach ($data as $item) {
            Variety::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncCategories($data)
    {
        $count = 0;
        foreach ($data as $item) {
            Category::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncUnits($data)
    {
        $count = 0;
        foreach ($data as $item) {
            Unit::updateOrCreate(
                ['name' => $item['name']],
                [
                    'symbol' => $item['symbol'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncWarehouses($data)
    {
        $count = 0;
        foreach ($data as $item) {
            Warehouse::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'location' => $item['location'] ?? null,
                    'capacity' => $item['capacity'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncCropCategories($data)
    {
        $count = 0;
        foreach ($data as $item) {
            CropCategory::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncCropTypes($data)
    {
        $count = 0;
        foreach ($data as $item) {
            CropType::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncVarietyTypes($data)
    {
        $count = 0;
        foreach ($data as $item) {
            VarietyType::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncSeasons($data)
    {
        $count = 0;
        foreach ($data as $item) {
            Season::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    private function syncSeedClasses($data)
    {
        $count = 0;
        foreach ($data as $item) {
            SeedClass::updateOrCreate(
                ['name' => $item['name']],
                [
                    'code' => $item['code'] ?? null,
                    'description' => $item['description'] ?? null,
                ]
            );
            $count++;
        }
        return $count;
    }

    public function locationIndex()
    {
        return view('sync.location');
    }

    public function syncLocation(Request $request)
    {
        $type = $request->input('type');
        
        try {
            $count = 0;
            
            switch ($type) {
                case 'countries':
                    $count = $this->syncCountries();
                    break;
                case 'states':
                    $count = $this->syncStates();
                    break;
                case 'cities':
                    $count = $this->syncCities();
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid type'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$count} {$type}",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error("Location Sync Error ({$type}): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function syncAllLocations()
    {
        try {
            $countries = $this->syncCountries();
            $states = $this->syncStates();
            $cities = $this->syncCities();

            return response()->json([
                'success' => true,
                'message' => 'All locations synced successfully',
                'countries' => $countries,
                'states' => $states,
                'cities' => $cities
            ]);

        } catch (\Exception $e) {
            Log::error('Location Sync All Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLocationCounts()
    {
        return response()->json([
            'countries' => Country::count(),
            'states' => State::count(),
            'cities' => City::count()
        ]);
    }

    private function syncCountries()
    {
        // API URL for countries - using a free public API
        $apiUrl = 'https://restcountries.com/v3.1/all';
        
        $response = Http::get($apiUrl);
        
        if (!$response->successful()) {
            throw new \Exception('Failed to fetch countries from API');
        }

        $data = $response->json();
        $count = 0;

        foreach ($data as $item) {
            Country::updateOrCreate(
                ['name' => $item['name']['common']],
                [
                    'code' => $item['cca2'] ?? null,
                    'iso2' => $item['cca2'] ?? null,
                    'iso3' => $item['cca3'] ?? null,
                ]
            );
            $count++;
        }

        return $count;
    }

    private function syncStates()
    {
        // Using a sample API - replace with your actual API
        // For demonstration, we'll use India's states
        $apiUrl = 'https://api.countrystatecity.in/v1/countries/IN/states';
        
        $response = Http::withHeaders([
            'X-CSCAPI-KEY' => config('services.countrystatecity.key', 'YOUR_API_KEY_HERE')
        ])->get($apiUrl);
        
        if (!$response->successful()) {
            // Fallback: create some sample states if API fails
            return $this->createSampleStates();
        }

        $data = $response->json();
        $count = 0;

        // Get India country
        $country = Country::where('name', 'India')->orWhere('iso2', 'IN')->first();
        
        if (!$country) {
            $country = Country::create([
                'name' => 'India',
                'code' => 'IN',
                'iso2' => 'IN',
                'iso3' => 'IND'
            ]);
        }

        foreach ($data as $item) {
            State::updateOrCreate(
                [
                    'name' => $item['name'],
                    'country_id' => $country->id
                ],
                [
                    'code' => $item['iso2'] ?? null,
                ]
            );
            $count++;
        }

        return $count;
    }

    private function syncCities()
    {
        // Sample implementation - replace with your actual API
        // This creates sample cities for existing states
        $states = State::all();
        $count = 0;

        foreach ($states as $state) {
            // You can replace this with actual API call
            // For now, creating sample cities
            $sampleCities = ['City A', 'City B', 'City C'];
            
            foreach ($sampleCities as $cityName) {
                City::updateOrCreate([
                    'name' => $cityName . ' - ' . $state->name,
                    'state_id' => $state->id
                ]);
                $count++;
            }
        }

        return $count;
    }

    private function createSampleStates()
    {
        $country = Country::where('name', 'India')->orWhere('iso2', 'IN')->first();
        
        if (!$country) {
            $country = Country::create([
                'name' => 'India',
                'code' => 'IN',
                'iso2' => 'IN',
                'iso3' => 'IND'
            ]);
        }

        $states = [
            ['name' => 'Maharashtra', 'code' => 'MH'],
            ['name' => 'Gujarat', 'code' => 'GJ'],
            ['name' => 'Karnataka', 'code' => 'KA'],
            ['name' => 'Tamil Nadu', 'code' => 'TN'],
            ['name' => 'Delhi', 'code' => 'DL'],
        ];

        $count = 0;
        foreach ($states as $stateData) {
            State::updateOrCreate(
                [
                    'name' => $stateData['name'],
                    'country_id' => $country->id
                ],
                [
                    'code' => $stateData['code']
                ]
            );
            $count++;
        }

        return $count;
    }
}
