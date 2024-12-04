Backend: Weather API
Overview
This is the backend for the Weather App, built using Laravel 10. It provides a RESTful API for weather data and forecasts, fetched from OpenWeather APIs.

Features
Open REST API to fetch:
Current weather by city.
Weather forecasts.
Simple and efficient Laravel setup.
Tech Stack
Framework: Laravel 10
HTTP Client: Guzzle
PHP Version: ^8.1
Setup
Clone the Repository:

bash
Copy code
git clone https://github.com/juicecola/weather-backend
cd weather-backend
Install Dependencies:

bash
Copy code
composer install
Set Environment Variables: Copy .env.example to .env and update the OPENWEATHER_API_KEY:

bash
Copy code
cp .env.example .env
Run the Application:

bash
Copy code
php artisan serve
The API will be accessible at http://localhost:8000.

API Endpoints
GET /api/weather?city={city}: Fetch current weather and forecast for a city.
File Structure
routes/api.php: Defines API routes.
app/Http/Controllers/WeatherController.php: Contains API logic.
Extras
Robust error handling for API failures and invalid inputs.
Consistent JSON responses for seamless integration.
