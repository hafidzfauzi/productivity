<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function show()
    {
        $latitude = request()->query('latitude');
        $longitude = request()->query('longitude');

        // Default to Cilacap Utara coordinates if not provided to sync with prayer times
        $lat = $latitude ?? -7.7011;
        $lon = $longitude ?? 109.0233;

        $cacheKey = 'weather_data_' . round($lat, 2) . '_' . round($lon, 2);

        $data = Cache::remember($cacheKey, 1800, function () use ($lat, $lon, $latitude, $longitude) {
            $apiKey = config('services.openweathermap.key');

            if (!$apiKey) {
                return $this->fallbackWeather($lat, $lon, $latitude && $longitude);
            }

            try {
                $response = Http::timeout(5)->get('https://api.openweathermap.org/data/2.5/weather', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);

                if ($response->successful()) {
                    $json = $response->json();

                    return [
                        'temp' => round($json['main']['temp']),
                        'condition' => $json['weather'][0]['description'],
                        'icon' => $this->mapIcon($json['weather'][0]['icon']),
                        'city' => $json['name'],
                        'humidity' => $json['main']['humidity'],
                    ];
                }
            } catch (\Exception $e) {
                // Fallback
            }

            return $this->fallbackWeather($lat, $lon, $latitude && $longitude);
        });

        return response()->json($data);
    }

    private function fallbackWeather(float $lat, float $lon, bool $hasGps = false): array
    {
        $city = 'Cilacap Utara';

        if ($hasGps) {
            try {
                // Fetch actual city name using free OpenStreetMap Nominatim API
                $response = Http::timeout(3)
                    ->withHeaders(['User-Agent' => 'FocusHub-App'])
                    ->get('https://nominatim.openstreetmap.org/reverse', [
                        'format' => 'json',
                        'lat' => $lat,
                        'lon' => $lon,
                    ]);

                if ($response->successful()) {
                    $address = $response->json()['address'] ?? [];
                    // Pick the cleanest local name, avoiding village names if county/city is available
                    $cityLabel = null;
                    if (isset($address['city']) && !str_contains(strtolower($address['city']), 'desa') && !str_contains(strtolower($address['city']), 'kelurahan')) {
                        $cityLabel = $address['city'];
                    }
                    $city = $cityLabel 
                        ?? $address['county'] 
                        ?? $address['town'] 
                        ?? $address['city'] 
                        ?? $address['suburb'] 
                        ?? $address['village'] 
                        ?? 'GPS Location';
                }
            } catch (\Exception $e) {
                // Fallback to simple label
                $city = 'GPS Location';
            }
        }

        return [
            'temp' => 29,
            'condition' => 'Partly cloudy',
            'icon' => '⛅',
            'city' => $city,
            'humidity' => 75,
        ];
    }

    private function mapIcon(string $iconCode): string
    {
        return match (true) {
            str_starts_with($iconCode, '01') => '☀️',
            str_starts_with($iconCode, '02') => '⛅',
            str_starts_with($iconCode, '03') => '☁️',
            str_starts_with($iconCode, '04') => '☁️',
            str_starts_with($iconCode, '09') => '🌧️',
            str_starts_with($iconCode, '10') => '🌦️',
            str_starts_with($iconCode, '11') => '⛈️',
            str_starts_with($iconCode, '13') => '❄️',
            str_starts_with($iconCode, '50') => '🌫️',
            default => '🌤️',
        };
    }
}
