Weather API (Laravel)
This is the backend for the Weather App, providing APIs to fetch current weather and forecasts for cities using OpenWeather APIs.

Features
Open access REST API for weather data.
Handles city-based geolocation using OpenWeather’s Geocoding API.
Lightweight and efficient setup with Laravel 10.
Tech Stack
Framework: Laravel 10
HTTP Client: Guzzle
PHP Version: ^8.1
Setup Instructions
Clone the Repository:

bash
Copy code
git clone <repository-url>
cd weather-backend
Install Dependencies:

bash
Copy code
composer install
Set Up Environment Variables:

Copy .env.example to .env:
bash
Copy code
cp .env.example .env
Add your OpenWeather API key:
env
Copy code
OPENWEATHER_API_KEY=your_api_key_here
Run the Application:

bash
Copy code
php artisan serve
The API will be available at http://localhost:8000.

API Endpoints
GET /api/weather?city={city}: Retrieves current weather and forecast for the specified city.
File Structure
routes/api.php: Defines API routes.
app/Http/Controllers/WeatherController.php: Handles API logic.
config/: Includes configuration files.
Extras
Error handling ensures users get clear feedback for invalid inputs or API issues.
Modular and maintainable code for easy scaling.
