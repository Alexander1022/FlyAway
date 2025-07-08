<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Services\LocationService;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index(Request $request)
    {
        return $this->locationService->index($request);
    }

    public function show(Location $location)
    {
        return $this->locationService->show($location);
    }
    public function store(LocationRequest $request)
    {
        return $this->locationService->store($request);
    }

    public function update(LocationRequest $request, Location $location)
    {
        return $this->locationService->update($request, $location);
    }

    public function getUserLocations()
    {
        return $this->locationService->getUserLocations();
    }

    public function destroy(Location $location)
    {
        return $this->locationService->destroy($location);
    }
}
