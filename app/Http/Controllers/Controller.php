<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        // Validate city input
        $request->validate([
            'city' => 'required|string'
        ]);

        $city = $request->input('city');
        $apiKey = env('OPENWEATHER_API_KEY');

        try {
            // Geocoding API call
            $geoResponse = Http::get("http://api.openweathermap.org/geo/1.0/direct", [
                'q' => $city,
                'limit' => 1,
                'appid' => $apiKey
            ]);

            // Check if geocoding was successful
            if ($geoResponse->failed() || empty($geoResponse->json())) {
                return response()->json([
                    'error' => 'City not found'
                ], 404);
            }

            $geoData = $geoResponse->json()[0];

            // Weather data API call
            $weatherResponse = Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                'lat' => $geoData['lat'],
                'lon' => $geoData['lon'],
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            // Check if weather data retrieval was successful
            if ($weatherResponse->failed()) {
                return response()->json([
                    'error' => 'Unable to fetch weather data'
                ], 500);
            }

            return response()->json([
                'location' => [
                    'name' => $geoData['name'],
                    'country' => $geoData['country']
                ],
                'geo' => $geoData,
                'weather' => $weatherResponse->json()
            ]);

        } catch (\Exception $e) {
            // Log the error and return a generic error response
            \Log::error('Weather API Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'An unexpected error occurred'
            ], 500);
        }
    }
}
