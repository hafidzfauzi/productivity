<?php

namespace App\Http\Controllers;

use App\Models\FocusSession;
use App\Models\GamificationStat;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FocusSessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode' => 'required|in:focus,short,long,nap',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $session = FocusSession::create([
            'mode' => $validated['mode'],
            'duration_minutes' => $validated['duration_minutes'],
            'completed_at' => now(),
        ]);

        // Update gamification if focus mode
        if ($validated['mode'] === 'focus') {
            $this->updateGamification();
        }

        return response()->json($session, 201);
    }

    public function todayStats()
    {
        $today = Carbon::today();

        $sessions = FocusSession::whereDate('completed_at', $today)
            ->where('mode', 'focus')
            ->count();

        $totalMinutes = FocusSession::whereDate('completed_at', $today)
            ->where('mode', 'focus')
            ->sum('duration_minutes');

        return response()->json([
            'sessions' => $sessions,
            'total_minutes' => $totalMinutes,
        ]);
    }

    private function updateGamification(): void
    {
        $stat = GamificationStat::first();
        if (!$stat) {
            $stat = GamificationStat::create([
                'total_xp' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]);
        }

        // Add XP
        $stat->total_xp += 50;

        // Update streak
        $today = Carbon::today();
        $lastActive = $stat->last_active_date ? Carbon::parse($stat->last_active_date) : null;

        if (!$lastActive || $lastActive->lt($today)) {
            if ($lastActive && $lastActive->eq($today->copy()->subDay())) {
                // Consecutive day
                $stat->current_streak += 1;
            } elseif (!$lastActive || $lastActive->lt($today->copy()->subDay())) {
                // Streak broken
                $stat->current_streak = 1;
            }

            $stat->last_active_date = $today;
        }

        if ($stat->current_streak > $stat->longest_streak) {
            $stat->longest_streak = $stat->current_streak;
        }

        $stat->save();
    }
}
