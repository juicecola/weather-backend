<?php

namespace Tests\Feature;

use App\Models\WeatherSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_logs_weather_search_to_database()
    {
        // Simulate API call
        $this->postJson('/api/weather', [
            'city' => 'Nairobi',
        ]);

        // Assert that a record was created
        $this->assertDatabaseHas('weather_searches', [
            'city' => 'Nairobi',
        ]);
    }
}

