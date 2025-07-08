<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAchievementRequest;
use App\Services\UserAchievementService;

class UserAchievementController
{
    protected $userAchievementService;

    public function __construct(UserAchievementService $userAchievementService)
    {
        $this->userAchievementService = $userAchievementService;
    }

    public function assignPoints(UserAchievementRequest $request)
    {
        return $this->userAchievementService->assignPoints($request);    
    }
}