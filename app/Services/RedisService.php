<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RedisService
{
    /**
     * Cache car data with Redis
     */
    public static function cacheCar($carId, $data, $ttl = 3600)
    {
        $key = "car:{$carId}";
        Cache::store('redis')->put($key, $data, $ttl);
    }

    /**
     * Get cached car data
     */
    public static function getCachedCar($carId)
    {
        $key = "car:{$carId}";
        return Cache::store('redis')->get($key);
    }

    /**
     * Cache car list with pagination
     */
    public static function cacheCarList($key, $data, $ttl = 1800)
    {
        Cache::store('redis')->put($key, $data, $ttl);
    }

    /**
     * Get cached car list
     */
    public static function getCachedCarList($key)
    {
        return Cache::store('redis')->get($key);
    }

    /**
     * Cache popular makes
     */
    public static function cachePopularMakes($makes, $ttl = 3600)
    {
        Cache::store('redis')->put('popular_makes', $makes, $ttl);
    }

    /**
     * Get cached popular makes
     */
    public static function getCachedPopularMakes()
    {
        return Cache::store('redis')->get('popular_makes');
    }

    /**
     * Cache popular cities
     */
    public static function cachePopularCities($cities, $ttl = 3600)
    {
        Cache::store('redis')->put('popular_cities', $cities, $ttl);
    }

    /**
     * Get cached popular cities
     */
    public static function getCachedPopularCities()
    {
        return Cache::store('redis')->get('popular_cities');
    }

    /**
     * Increment car view count
     */
    public static function incrementCarViews($carId)
    {
        $key = "car:views:{$carId}";
        Redis::incr($key);
        Redis::expire($key, 86400); // Expire after 24 hours
    }

    /**
     * Get car view count
     */
    public static function getCarViews($carId)
    {
        $key = "car:views:{$carId}";
        return Redis::get($key) ?? 0;
    }

    /**
     * Cache search results
     */
    public static function cacheSearchResults($query, $results, $ttl = 600)
    {
        $key = 'search:' . md5($query);
        Cache::store('redis')->put($key, $results, $ttl);
    }

    /**
     * Get cached search results
     */
    public static function getCachedSearchResults($query)
    {
        $key = 'search:' . md5($query);
        return Cache::store('redis')->get($key);
    }

    /**
     * Clear car cache
     */
    public static function clearCarCache($carId)
    {
        $key = "car:{$carId}";
        Cache::store('redis')->forget($key);
    }

    /**
     * Clear all car-related cache
     */
    public static function clearAllCarCache()
    {
        Cache::store('redis')->flush();
    }

    /**
     * Store user session data in Redis
     */
    public static function storeUserSession($userId, $data, $ttl = 3600)
    {
        $key = "user:session:{$userId}";
        Redis::setex($key, $ttl, json_encode($data));
    }

    /**
     * Get user session data from Redis
     */
    public static function getUserSession($userId)
    {
        $key = "user:session:{$userId}";
        $data = Redis::get($key);
        return $data ? json_decode($data, true) : null;
    }

    /**
     * Store real-time car status updates
     */
    public static function storeCarStatusUpdate($carId, $status, $ttl = 300)
    {
        $key = "car:status:{$carId}";
        $data = [
            'status' => $status,
            'updated_at' => now()->toIso8601String(),
        ];
        Redis::setex($key, $ttl, json_encode($data));
    }

    /**
     * Get car status update
     */
    public static function getCarStatusUpdate($carId)
    {
        $key = "car:status:{$carId}";
        $data = Redis::get($key);
        return $data ? json_decode($data, true) : null;
    }

    /**
     * Publish to Redis channel (for broadcasting)
     */
    public static function publish($channel, $message)
    {
        Redis::publish($channel, json_encode($message));
    }

    /**
     * Get Redis connection health
     */
    public static function healthCheck()
    {
        try {
            Redis::ping();
            return ['status' => 'healthy', 'connected' => true];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'connected' => false, 'error' => $e->getMessage()];
        }
    }
}

