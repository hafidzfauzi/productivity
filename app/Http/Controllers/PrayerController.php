<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PrayerController extends Controller
{
    public function show()
    {
        $latitude = request()->query('latitude');
        $longitude = request()->query('longitude');

        // Default coordinates: Mertasinga (-7.68, 109.06)
        $lat = $latitude ?? -7.68;
        $lon = $longitude ?? 109.06;

        $today = Carbon::today('Asia/Jakarta')->format('d-m-Y');
        
        // Cache key includes rounded coordinates to prevent collisions but allow caching
        $cacheKey = "prayer_timings_raw_{$today}_" . round($lat, 2) . "_" . round($lon, 2);

        $timings = Cache::remember($cacheKey, 86400, function () use ($today, $lat, $lon) {
            try {
                $response = Http::timeout(5)->get('https://api.aladhan.com/v1/timings/' . $today, [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'method' => 20, // Kemenag Indonesia
                ]);

                if ($response->successful()) {
                    return $response->json()['data']['timings'];
                }
            } catch (\Exception $e) {
                // Fallback will use fallback raw timings
            }

            return null;
        });

        $prayerNames = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
        $prayerLabels = [
            'Fajr' => 'Subuh',
            'Dhuhr' => 'Dzuhur',
            'Asr' => 'Ashar',
            'Maghrib' => 'Maghrib',
            'Isha' => 'Isya',
        ];

        if (!$timings) {
            // Mertasinga fallback timings
            $timings = [
                'Fajr' => '04:37',
                'Dhuhr' => '11:54',
                'Asr' => '15:16',
                'Maghrib' => '17:47',
                'Isha' => '19:02',
            ];
        }

        $times = [];
        $now = Carbon::now('Asia/Jakarta');
        $nextPrayer = null;

        // Find the next prayer today
        foreach ($prayerNames as $prayer) {
            $timeStr = substr($timings[$prayer], 0, 5); // HH:MM
            $prayerTime = Carbon::createFromFormat('H:i', $timeStr, 'Asia/Jakarta');

            if (!$nextPrayer && $prayerTime->gt($now)) {
                $nextPrayer = [
                    'key' => $prayer,
                    'name' => $prayerLabels[$prayer],
                    'time' => $timeStr,
                ];
            }
        }

        // If all prayers passed today, next is Fajr tomorrow
        $isFajrTomorrow = false;
        if (!$nextPrayer) {
            $nextPrayer = [
                'key' => 'Fajr',
                'name' => $prayerLabels['Fajr'],
                'time' => substr($timings['Fajr'], 0, 5),
            ];
            $isFajrTomorrow = true;
        }

        foreach ($prayerNames as $prayer) {
            $timeStr = substr($timings[$prayer], 0, 5);
            $isNext = ($nextPrayer['key'] === $prayer) && !$isFajrTomorrow;

            $times[] = [
                'name' => $prayerLabels[$prayer],
                'time' => $timeStr,
                'isNext' => $isNext,
            ];
        }

        $data = [
            'nextName' => $nextPrayer['name'],
            'nextTime' => $nextPrayer['time'],
            'times' => $times,
        ];

        return response()->json($data);
    }

    private function fallbackPrayerTimes(): array
    {
        return [
            'nextName' => 'Dzuhur',
            'nextTime' => '12:05',
            'times' => [
                ['name' => 'Subuh', 'time' => '04:35', 'isNext' => false],
                ['name' => 'Dzuhur', 'time' => '12:05', 'isNext' => true],
                ['name' => 'Ashar', 'time' => '15:20', 'isNext' => false],
                ['name' => 'Maghrib', 'time' => '17:50', 'isNext' => false],
                ['name' => 'Isya', 'time' => '19:05', 'isNext' => false],
            ],
        ];
    }
}
