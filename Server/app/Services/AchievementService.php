<?php

namespace App\Services;

use App\Models\Achievement;
use App\Http\Resources\AchievementResource;
use App\Http\Requests\AchievementRequest;

class AchievementService
{
    public function index()
    {
        $achievements = Achievement::with('users', 'specieTypes')->get();
        return AchievementResource::collection($achievements);
    }

    public function show(Achievement $achievement)
    {
        $achievement->load('users', 'specieTypes');
        return new AchievementResource($achievement);
    }

    public function store(AchievementRequest $request)
    {
        $achievement = Achievement::create($request->validated());
        $specieTypes = $request->input('specie_types', []);
        $achievement->specieTypes()->sync($specieTypes);
        
        $achievement->load('users', 'specieTypes');
        return new AchievementResource($achievement);
    }

    public function update(AchievementRequest $request, Achievement $achievement)
    {
        $achievement->update($request->validated());
        
        $specieTypes = $request->input('specie_types', []);
        $achievement->specieTypes()->sync($specieTypes);
        
        $achievement->load('users', 'specieTypes');
        return new AchievementResource($achievement);
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();
        return response()->json(['message' => 'Achievement deleted successfully.'], 200);
    }
}
