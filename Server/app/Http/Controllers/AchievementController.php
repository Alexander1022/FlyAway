<?php

namespace App\Http\Controllers;

use App\Http\Requests\AchievementRequest;
use App\Services\AchievementService;
use App\Models\Achievement;

class AchievementController
{
    protected $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function index()
    {
        return $this->achievementService->index();
    }

    public function show(Achievement $achievement)
    {
        return $this->achievementService->show($achievement);
    }

    public function store(AchievementRequest $request)
    {
        return $this->achievementService->store($request);    
    }

    public function update(AchievementRequest $request, Achievement $achievement)
    {
        return $this->achievementService->update($request, $achievement);
    }

    public function destroy(Achievement $achievement)
    {
        return $this->achievementService->destroy($achievement);
    }
}