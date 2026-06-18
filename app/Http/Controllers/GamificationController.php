<?php

namespace App\Http\Controllers;

use App\Models\FocusSession;
use App\Models\GamificationStat;

class GamificationController extends Controller
{
    private const XP_PER_LEVEL = 600;

    public function show()
    {
        $stat = GamificationStat::first();

        if (!$stat) {
            $stat = GamificationStat::create([
                'total_xp' => 0,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]);
        }

        $totalSessions = FocusSession::where('mode', 'focus')->count();
        $xpInLevel = $stat->total_xp % self::XP_PER_LEVEL;
        $level = (int) floor($stat->total_xp / self::XP_PER_LEVEL) + 1;
        $currentStage = min(5, (int) floor($xpInLevel / 120));

        return response()->json([
            'totalSessions' => $totalSessions,
            'totalXp' => $stat->total_xp,
            'level' => $level,
            'xpInLevel' => $xpInLevel,
            'xpPerLevel' => self::XP_PER_LEVEL,
            'xpPercent' => self::XP_PER_LEVEL > 0 ? round(($xpInLevel / self::XP_PER_LEVEL) * 100, 1) : 0,
            'streak' => $stat->current_streak,
            'longestStreak' => $stat->longest_streak,
            'currentStage' => $currentStage,
        ]);
    }
}
