<?php

namespace App\Services;

use App\Models\User;
use App\Http\Resources\LeaderboardResource;

class LeaderboardService
{
    public function getLeaderboard()
    {
        $users = User::get();
        return LeaderboardResource::collection($users);
    }

    public function update($id, array $data)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($data);
            return new LeaderboardResource($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User removed from leaderboard successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}
