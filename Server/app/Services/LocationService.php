<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Specie;
use App\Models\FileType;
use App\Models\FileRecord;
use App\Models\Achievement;
use App\Http\Resources\LocationResource;
use App\Http\Requests\LocationRequest;
use Illuminate\Http\Request;
use App\Http\Requests\SpecieRequest;
use App\Services\SpecieService;
use App\Models\SpecieKingdom;

class LocationService
{
    protected $specieService;

    public function __construct(SpecieService $specieService)
    {
        $this->specieService = $specieService;
    }
    public function index(Request $request)
    {
        $query = Location::with(['images', 'user', 'specie.habitat', 'specie.SpecieKingdom', 'specie.specieTypes', 'specie.image']);
        $lat = $request->query('lat');
        $lng = $request->query('lng');
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kilometers = $request->query('kilometers');
        $specieIds = $request->query('specie_ids');

        if ($lat && $lng && $kilometers) {
            $query->whereRaw("ST_Distance_Sphere(point(lng, lat), point(?, ?)) <= ?", [$lng, $lat, $kilometers * 1000]);
        }

        if ($search) {
            $query->whereHas('specie', function ($q) use ($search) {
                $q->where('common_name', 'like', '%' . $search . '%')
                    ->orWhere('scientific_name', 'like', '%' . $search . '%');
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($specieIds) {
            $query->whereHas('specie', function ($q) use ($specieIds) {
                $q->whereIn('id', $specieIds);
            });
        }

        $locations = $query->get();
        return LocationResource::collection($locations);
    }

    public function show(Location $location)
    {
        $location->load('images', 'user', 'specie.habitat', 'specie.specieKingdom', 'specie.specieTypes', 'specie.image');

        return new LocationResource($location);
    }

    public function store(LocationRequest $request)
    {
        $data = $request->validated();
        $images = $request->file('images');
        $specieKingdom = $request->input('specie_kingdom');

        $speciesGroups = [];
        $client = new \GuzzleHttp\Client();

        foreach ($images as $image) {
            if (!$image->isValid()) {
                continue;
            }
            try {
                $response = $client->post(config('services.flask_ai.url') . "?type=" . $specieKingdom, [
                    'headers' => [
                        'X-API-Key' => config('services.flask_ai.api_key'),
                    ],
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => file_get_contents($image),
                            'filename' => basename($image)
                        ]
                    ]
                ]);

                if ($response->getStatusCode() !== 200) {
                    return response()->json(['message' => 'AI server down', 'error' => 'ai_server_down'], 503);
                }

                $result = json_decode($response->getBody()->getContents(), true);
                $species = $result['species_name'];
                $confidence = $result['confidence'];

                if ($confidence < 0.4) {
                    continue;
                }

                if (!isset($speciesGroups[$species])) {
                    $speciesGroups[$species] = [
                        'images' => [],
                        'highest_confidence' => 0,
                        'best_image' => null
                    ];
                }

                $speciesGroups[$species]['images'][] = $image;

                if ($confidence > $speciesGroups[$species]['highest_confidence']) {
                    $speciesGroups[$species]['highest_confidence'] = $confidence;
                    $speciesGroups[$species]['best_image'] = $image;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        if (empty($speciesGroups)) {
            return response()->json(['message' => 'No species with confidence above threshold'], 404);
        }

        $bestSpecies = '';
        $highestConfidence = 0;

        foreach ($speciesGroups as $species => $speciesData) {
            if ($speciesData['highest_confidence'] > $highestConfidence) {
                $highestConfidence = $speciesData['highest_confidence'];
                $bestSpecies = $species;
            }
        }
        $specie = Specie::where('scientific_name', $bestSpecies)->first();
        if (!$specie) {
            $bestImage = $speciesGroups[$bestSpecies]['best_image'];

            $specieKingdomId= SpecieKingdom::where('name', ucfirst($specieKingdom))->first()->id;

            $specie = $this->specieService->storeWithParams([
                'common_name' => $bestSpecies,
                'scientific_name' => $bestSpecies,
                'specie_kingdom_id' => $specieKingdomId,
                'habitat_id' => null,
            ], $bestImage);

            if (!$specie) {
                return response()->json(['message' => 'Failed to find specie'], 404);
            }
        }

        $data['user_id'] = auth()->user()->id;
        $data['specie_id'] = $specie->id;

        $data['confidence'] = $highestConfidence;
        $data['species_name'] = $bestSpecies;

        $location = Location::create($data);

        foreach ($speciesGroups[$bestSpecies]['images'] as $image) {
            $fileType = FileType::where('name', 'image')->firstOrFail();
            $path = $image->store('locations', 'public');
            $fileRecord = new FileRecord([
                'path' => 'storage/' . $path,
                'original_name' => $image->getClientOriginalName(),
                'file_type_id' => $fileType->id,
                'fileable_type' => Location::class,
                'fileable_id' => $location->id
            ]);
            $fileRecord->save();
        }

        $achievements = Achievement::whereHas('specieTypes', function ($query) use ($specie) {
            $query->where('specie_type_id', $specie->id);
        })->get();

        foreach ($achievements as $achievement) {
            $userAchievement = $achievement->users()
                ->wherePivot('user_id', auth()->user()->id)
                ->first();

            if (!$userAchievement) {
                $achievement->users()->attach(auth()->user()->id, ['points' => 1]);

                if ($achievement->points_to_complete <= 1) {
                    $user = auth()->user();
                    $user->xp += $achievement->reward_xp;
                    $user->save();
                }
            } else {
                $currentPoints = $userAchievement->pivot->points;

                if ($currentPoints < $achievement->points_to_complete) {
                    $newPoints = $currentPoints + 1;

                    $achievement->users()->updateExistingPivot(auth()->user()->id, [
                        'points' => $newPoints
                    ]);

                    if ($newPoints == $achievement->points_to_complete) {
                        $user = auth()->user();
                        $user->xp += $achievement->reward_xp;
                        $user->save();
                    }
                }
            }
        }

        $location->load('images', 'user', 'specie');

        return response()->json(['message' => 'Location created successfully', 'fun_fact' => $this->mockFunFact($bestSpecies), 'location' => new LocationResource($location)]);
    }

    public function getUserLocations()
    {
        $user = auth()->user();
        $locations = Location::where('user_id', $user->id)->with('images', 'specie')->get();
        return LocationResource::collection($locations);
    }

    public function update(LocationRequest $request, Location $location)
    {
        // Check if the authenticated user owns this location
        if ($location->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized. You can only update your own locations.'], 403);
        }

        $data = $request->validated();
        $location->update($data);

        $location->load('images', 'user', 'specie');
        return new LocationResource($location);
    }

    public function destroy(Location $location)
    {
        // Check if the authenticated user owns this location
        if ($location->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized. You can only delete your own locations.'], 403);
        }

        $location->delete();
        return response()->json(['message' => 'Location deleted successfully.'], 200);
    }

    private function getFunFact($scientificName)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post(config('services.open_ai.url'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('services.open_ai.api_key')
                ],
                'json' => [
                    'model' => config('services.open_ai.model'),
                    'store' => true,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "Write a random fun fact about $scientificName"
                        ]
                    ]
                ]
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            if (isset($result['choices'][0]['message']['content'])) {
                return $result['choices'][0]['message']['content'];
            }

            return "No fun facts available for this specie.";
        } catch (\Exception $e) {
            \Log::error('Failed to get fun fact: ' . $e->getMessage());
            return "No fun facts available for this specie.";
        }
    }

    private function mockFunFact($scientificName)
    {
        return "Did you know that $scientificName can live up to 100 years?";
    }
}
