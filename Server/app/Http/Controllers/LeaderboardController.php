<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;
use Illuminate\Http\Request;

class LeaderboardController
{    
    protected $leaderboardService;

    public function __construct(
        LeaderboardService $leaderboardService
    ) {
        $this->leaderboardService = $leaderboardService;
    }
    

    public function getLeaderboard ()
    {
        return$this->leaderboardService->getLeaderboard();
    }

    public function update(Request $request, $id)
    {
        return $this->leaderboardService->update($id, $request->all());
    }

    public function destroy($id)
    {
        return $this->leaderboardService->destroy($id);
    }
}