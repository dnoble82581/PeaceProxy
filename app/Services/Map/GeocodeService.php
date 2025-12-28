<?php

namespace App\Services\Map;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeocodeService
{
    /**
     * Geocode a free-form address string to latitude and longitude using Google Geocoding API.
     *
     * @param  string  $address  The address to geocode
     * @return array{lat: float, lng: float}|null Returns [lat, lng] on success, or null if not found or on error
     */
    public function geocode(string $address): ?array
    {
        $address = trim($address);
        if ($address === '') {
            return null;
        }

        // Prefer backend key; fall back to JS key if needed (some projects use a single key)
        $key = (string) (config('services.maps.backend_key') ?: config('services.maps.js_key'));
        if ($key === '') {
            Log::warning('GeocodeService: Missing Google Maps API key (services.maps.backend_key or services.maps.js_key)');
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->retry(2, 250)
                ->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address,
                    'key' => $key,
                ]);

            if (! $response->ok()) {
                Log::warning('GeocodeService: Non-OK response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $status = Arr::get($data, 'status');
            if ($status !== 'OK') {
                Log::info('GeocodeService: Geocoding did not return OK', [
                    'status' => $status,
                    'error_message' => Arr::get($data, 'error_message'),
                ]);
                return null;
            }

            $location = Arr::get($data, 'results.0.geometry.location');
            if (! is_array($location) || ! isset($location['lat'], $location['lng'])) {
                return null;
            }

            return [
                'lat' => (float) $location['lat'],
                'lng' => (float) $location['lng'],
            ];
        } catch (Throwable $e) {
            Log::error('GeocodeService: Exception while geocoding', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
