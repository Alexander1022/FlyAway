import { useEffect, useState } from "react";
import { Trophy } from "lucide-react";
import axios from "axios";
import { useAuth } from "../auth/AuthContext";
import UserAvatar from "../assets/default-avatar.svg";

interface LeaderboardUser {
  id: string;
  name: string;
  avatarUrl: string;
  xp: number;
  observationsCount: number;
}

export default function Leaderboard() {
  const { user } = useAuth();
  const [leaderboard, setLeaderboard] = useState<LeaderboardUser[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchLeaderboard = async () => {
      try {
        const response = await axios.get(
          `${import.meta.env.VITE_SERVER_ENDPOINT}/api/leaderboard`,
          {
            headers: {
              "ngrok-skip-browser-warning": "please-ngrok-we-love-you",
              Authorization: `Bearer ${localStorage.getItem("authToken")}`,
            },
          }
        );

        setLeaderboard(response.data.data);
        setError(null);
      } catch (err) {
        if (axios.isAxiosError(err)) {
          const message = err.response?.data?.message || err.message;
          setError(message);
        } else {
          setError(err instanceof Error ? err.message : "Unknown error");
        }
      } finally {
        setLoading(false);
      }
    };

    fetchLeaderboard();
  }, []);

  return (
    <div className="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
      <div className="max-w-6xl mx-auto">
        {loading && (
          <div className="absolute inset-0 bg-gray-500/50 flex items-center justify-center z-50">
            <div className="text-white text-xl">Loading leaderboard...</div>
          </div>
        )}

        <div className="bg-white rounded-xl shadow-sm p-6 mb-6 lg:mb-8">
          <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <Trophy className="w-8 h-8 text-purple-600" />
            Community Leaderboard
          </h2>

          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-purple-50 text-left text-gray-700 rounded-t-lg">
                  <th className="px-4 py-3 rounded-tl-lg font-medium">Rank</th>
                  <th className="px-4 py-3 font-medium">User</th>
                  <th className="px-4 py-3 font-medium">XP</th>
                  <th className="px-4 py-3 rounded-tr-lg font-medium">
                    Observations
                  </th>
                </tr>
              </thead>
              <tbody>
                {leaderboard.map((entry, index) => (
                  <tr
                    key={entry.id}
                    className={`border-b border-gray-100 ${
                      entry.id === user?.data?.id
                        ? "bg-emerald-50"
                        : "hover:bg-gray-50"
                    }`}
                  >
                    <td className="px-4 py-3">
                      <div
                        className={`w-8 h-8 rounded-full flex items-center justify-center ${
                          index === 0
                            ? "bg-amber-400"
                            : index === 1
                            ? "bg-slate-300"
                            : index === 2
                            ? "bg-amber-600"
                            : "bg-gray-200"
                        }`}
                      >
                        <span
                          className={`text-sm font-medium ${
                            index < 3 ? "text-white" : "text-gray-700"
                          }`}
                        >
                          {index + 1}
                        </span>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-3">
                        <img
                          src={UserAvatar}
                          alt={entry.name}
                          className="w-10 h-10 rounded-full object-cover border-2 border-emerald-100"
                        />
                        <span className="font-medium">{entry.name}</span>
                        {entry.id === user?.data?.id && (
                          <span className="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-sm">
                            You
                          </span>
                        )}
                      </div>
                    </td>
                    <td className="px-4 py-3 font-medium text-purple-600">
                      {entry.xp}
                    </td>
                    <td className="px-4 py-3 text-gray-600">
                      {entry.observationsCount}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {error && (
            <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-600">
              Error loading leaderboard: {error}
            </div>
          )}

          {leaderboard.length === 0 && !loading && (
            <div className="text-center py-6 text-gray-500">
              No leaderboard data available
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
