<?php

namespace App\Http\Controllers;

use App\Services\HabitatService;
use App\Http\Requests\HabitatRequest;
use App\Models\Habitat;

class HabitatController
{
    protected $habitatService;

    public function __construct(HabitatService $habitatService)
    {
        $this->habitatService = $habitatService;
    }

    public function index()
    {
        return $this->habitatService->index();
    }

    public function show(Habitat $habitat)
    {
        return  $this->habitatService->show($habitat);
    }

    public function store(HabitatRequest $request)
    {
        return $this->habitatService->store($request);
    }
    
    public function update(HabitatRequest $request, Habitat $habitat)
    {
        return $this->habitatService->update($request, $habitat);
    }

    public function destroy(Habitat $habitat)
    {
        return $this->habitatService->destroy($habitat);
    }
    public function dropdown()
    {
        return $this->habitatService->dropdown();
    }
}