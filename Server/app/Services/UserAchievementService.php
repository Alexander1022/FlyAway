<?php

namespace App\Services;

use App\Models\Achievement;
use App\Http\Resources\AchievementResource;
use App\Http\Requests\UserAchievementRequest;

class UserAchievementService
{
    public function assignPoints(UserAchievementRequest $request)
    {
        $achievementId = $request->validated('achievement_id');
        $user = auth()->user();
        
        // Find achievement or return 404
        $achievement = Achievement::find($achievementId);
        if (!$achievement) {
            return response()->json(['message' => 'Achievement not found.'], 404);
        }
        
        // Check if relationship already exists
        $exists = $user->achievements()->where('achievement_id', $achievementId)->exists();
        
        // Create or update the relationship with points
        $user->achievements()->syncWithoutDetaching([
            $achievementId => ['points' => $request->validated('points')]
        ]);
        
        $message = $exists 
            ? 'Achievement points updated successfully.' 
            : 'Achievement assigned successfully.';
        
        return response()->json(['message' => $message], 200);
    }
}
