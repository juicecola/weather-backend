<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends BaseController
{
    public function getWeather(Request $request)
    {
        try {
            // Default to London if no city provided
            $city = $request->input('city', 'London');
            $apiKey = env('OPENWEATHER_API_KEY');

            Log::info('Weather API Request', [
                'city' => $city,
                'apiKey' => substr($apiKey, 0, 5) . '...'
            ]);

            // Geocoding API call to get latitude and longitude
            $geoResponse = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
                'q' => $city,
                'limit' => 1,
                'appid' => $apiKey
            ]);

            if ($geoResponse->failed()) {
                return response()->json([
                    'error' => 'Unable to fetch geocoding data',
                    'details' => $geoResponse->body()
                ], 400);
            }

            $geoData = $geoResponse->json();

            if (empty($geoData)) {
                return response()->json([
                    'error' => 'City not found',
                    'city' => $city
                ], 404);
            }

            $lat = $geoData[0]['lat'];
            $lon = $geoData[0]['lon'];

            // Weather API call
            $weatherResponse = Http::get("https://api.openweathermap.org/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric'  // Use Celsius
            ]);

            if ($weatherResponse->failed()) {
                return response()->json([
                    'error' => 'Unable to fetch weather data',
                    'details' => $weatherResponse->body()
                ], 400);
            }

            $weatherData = $weatherResponse->json();

            return response()->json([
                'city' => $city,
                'temperature' => $weatherData['main']['temp'],
                'feels_like' => $weatherData['main']['feels_like'],
                'humidity' => $weatherData['main']['humidity'],
                'description' => $weatherData['weather'][0]['description'],
                'wind_speed' => $weatherData['wind']['speed']
            ]);

        } catch (\Exception $e) {
            Log::error('Weather API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}