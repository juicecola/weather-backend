<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WeatherController extends BaseController
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENWEATHER_API_KEY');
    }

    public function getCurrentWeather(Request $request)
    {
        try {
            $city = $request->input('city', 'Nairobi');
            $unit = $request->input('unit', 'metric');

            // Log the request for debugging
            Log::info('Weather Request', [
                'city' => $city, 
                'unit' => $unit
            ]);

            // Step 1: Geocoding to get coordinates
            $geoData = $this->getGeocoding($city);

            // Step 2: Current Weather
            $currentWeather = $this->fetchCurrentWeather(
                $geoData['lat'], 
                $geoData['lon'], 
                $unit
            );

            // Step 3: 5-Day Forecast
            $forecast = $this->fetchForecast(
                $geoData['lat'], 
                $geoData['lon'], 
                $unit
            );

            // Prepare response
            return response()->json([
                'location' => [
                    'city' => $geoData['name'],
                    'country' => $geoData['country']
                ],
                'current' => [
                    'temperature' => round($currentWeather['main']['temp'], 1),
                    'feels_like' => round($currentWeather['main']['feels_like'], 1),
                    'humidity' => $currentWeather['main']['humidity'],
                    'wind_speed' => $currentWeather['wind']['speed'],
                    'description' => $currentWeather['weather'][0]['description'],
                    'icon' => $currentWeather['weather'][0]['icon']
                ],
                'forecast' => $this->processForecastData($forecast, $unit)
            ]);

        } catch (\Exception $e) {
            Log::error('Weather API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to fetch weather data',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function getGeocoding(string $city)
    {
        $response = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
            'q' => $city,
            'limit' => 1,
            'appid' => $this->apiKey
        ]);

        $geoData = $response->json();

        if (empty($geoData)) {
            throw new \Exception("City not found: {$city}");
        }

        return [
            'name' => $geoData[0]['name'],
            'lat' => $geoData[0]['lat'],
            'lon' => $geoData[0]['lon'],
            'country' => $geoData[0]['country']
        ];
    }

    private function fetchCurrentWeather(float $lat, float $lon, string $unit)
    {
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => $unit
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch current weather");
        }

        return $response->json();
    }

    private function fetchForecast(float $lat, float $lon, string $unit)
    {
        $response = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apiKey,
            'units' => $unit
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to fetch forecast");
        }

        return $response->json();
    }

    private function processForecastData(array $forecastData, string $unit)
    {
        $processedForecasts = [];
        $uniqueDays = [];

        foreach ($forecastData['list'] as $forecast) {
            $date = Carbon::createFromTimestamp($forecast['dt']);
            $dayKey = $date->format('Y-m-d');

            // Ensure we only take one forecast per day
            if (!in_array($dayKey, $uniqueDays) && count($processedForecasts) < 3) {
                $processedForecasts[] = [
                    'date' => $date->format('Y-m-d'),
                    'day' => $date->format('l'),
                    'temperature' => round($forecast['main']['temp'], 1),
                    'description' => $forecast['weather'][0]['description'],
                    'icon' => $forecast['weather'][0]['icon']
                ];

                $uniqueDays[] = $dayKey;
            }
        }

        return $processedForecasts;
    }
}
