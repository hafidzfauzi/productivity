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

        // Default to Mertasinga coordinates if not provided to sync with prayer times
        $lat = $latitude ?? -7.68;
        $lon = $longitude ?? 109.06;

        $cacheKey = 'weather_data_' . round($lat, 2) . '_' . round($lon, 2);

        $data = Cache::remember($cacheKey, 300, function () use ($lat, $lon, $latitude, $longitude) {
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
        $city = 'Mertasinga';

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

        try {
            // Fetch real-time weather from Open-Meteo (completely free, no API key required)
            $weatherResponse = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $lon,
                'current' => 'temperature_2m,relative_humidity_2m,weather_code',
            ]);

            if ($weatherResponse->successful()) {
                $current = $weatherResponse->json()['current'];
                $meteoData = $this->mapMeteoWeather((int)$current['weather_code']);

                return [
                    'temp' => round($current['temperature_2m']),
                    'condition' => $meteoData['condition'],
                    'icon' => $meteoData['icon'],
                    'city' => $city,
                    'humidity' => $current['relative_humidity_2m'],
                ];
            }
        } catch (\Exception $e) {
            // Fallback to static mock data if API call fails
        }

        return [
            'temp' => 29,
            'condition' => 'Partly cloudy',
            'icon' => '⛅',
            'city' => $city,
            'humidity' => 75,
        ];
    }

    private function mapMeteoWeather(int $code): array
    {
        return match ($code) {
            0 => ['condition' => 'Clear sky', 'icon' => '☀️'],
            1, 2 => ['condition' => 'Partly cloudy', 'icon' => '⛅'],
            3 => ['condition' => 'Overcast', 'icon' => '☁️'],
            45, 48 => ['condition' => 'Foggy', 'icon' => '🌫️'],
            51, 53, 55 => ['condition' => 'Drizzle', 'icon' => '🌧️'],
            61, 63, 65 => ['condition' => 'Rainy', 'icon' => '🌧️'],
            71, 73, 75 => ['condition' => 'Snowy', 'icon' => '❄️'],
            80, 81, 82 => ['condition' => 'Rain showers', 'icon' => '🌦️'],
            95, 96, 99 => ['condition' => 'Thunderstorm', 'icon' => '⛈️'],
            default => ['condition' => 'Partly cloudy', 'icon' => '🌤️'],
        };
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
