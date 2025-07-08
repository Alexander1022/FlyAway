<?php

namespace App\Services;

use App\Models\Habitat;
use App\Http\Resources\HabitatResource;
use App\Http\Resources\HabitatDropdownResource;
use App\Http\Requests\HabitatRequest;

class HabitatService
{
    public function index()
    {
        $specieTypes = Habitat::with('species')->get();
        return HabitatResource::collection($specieTypes);
    }

    public function show(Habitat $habitat)
    {
        $habitat->load('species');
        return new HabitatResource($habitat);
    }

    public function store(HabitatRequest $request)
    {
        $validatedData = $request->validated();
        $specie = Habitat::create($validatedData);
        return new HabitatResource($specie);
    }

    public function update(HabitatRequest $request, Habitat $habitat)
    {
        $habitat->update($request->validated());
        return new HabitatResource($habitat);
    }

    public function destroy(Habitat $habitat)
    {
        $habitat->delete();
        return response()->json(['message' => 'Habitat deleted successfully.'], 200);
    }
    public function dropdown()
    {
        $habitats = Habitat::all();
        return HabitatDropdownResource::collection($habitats);
    } 
}
