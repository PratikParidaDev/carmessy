# Redis Integration Guide

This document explains how Redis is integrated into the Car Marketplace project.

## Prerequisites

1. **Install Redis Server**
   - Windows: Download from https://github.com/microsoftarchive/redis/releases or use WSL
   - Linux: `sudo apt-get install redis-server` or `sudo yum install redis`
   - macOS: `brew install redis`

2. **Start Redis Server**
   ```bash
   # Linux/macOS
   redis-server
   
   # Windows (if installed)
   redis-server
   ```

3. **Verify Redis is Running**
   ```bash
   redis-cli ping
   # Should return: PONG
   ```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Redis Configuration
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1

# Cache Configuration
CACHE_STORE=redis

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Queue Configuration (optional)
QUEUE_CONNECTION=redis
```

### Configuration Files

Redis is already configured in:
- `config/database.php` - Redis connection settings
- `config/cache.php` - Cache store configuration
- `config/session.php` - Session driver configuration
- `config/queue.php` - Queue driver configuration

## Usage Examples

### Using RedisService

The `RedisService` class provides helper methods for common Redis operations:

```php
use App\Services\RedisService;

// Cache a car
RedisService::cacheCar($carId, $carData, 3600); // Cache for 1 hour

// Get cached car
$cachedCar = RedisService::getCachedCar($carId);

// Increment view count
RedisService::incrementCarViews($carId);

// Get view count
$views = RedisService::getCarViews($carId);

// Clear cache
RedisService::clearCarCache($carId);
```

### Direct Cache Usage

```php
use Illuminate\Support\Facades\Cache;

// Store data
Cache::store('redis')->put('key', 'value', 3600);

// Retrieve data
$value = Cache::store('redis')->get('key');

// Check if exists
if (Cache::store('redis')->has('key')) {
    // Do something
}

// Forget cache
Cache::store('redis')->forget('key');
```

### Direct Redis Usage

```php
use Illuminate\Support\Facades\Redis;

// Set value
Redis::set('key', 'value');
Redis::setex('key', 3600, 'value'); // With expiration

// Get value
$value = Redis::get('key');

// Increment
Redis::incr('counter');
Redis::incrby('counter', 5);

// Lists
Redis::lpush('list', 'item');
Redis::rpop('list');

// Hashes
Redis::hset('hash', 'field', 'value');
Redis::hget('hash', 'field');
```

## Implementation in Controllers

### Example: Caching Car Data

```php
use App\Services\RedisService;
use App\Models\Car;

public function show(Car $car)
{
    // Try to get from cache
    $cachedCar = RedisService::getCachedCar($car->id);
    
    if ($cachedCar) {
        return view('cars.show', ['car' => $cachedCar]);
    }
    
    // Load from database
    $car->load(['make', 'model', 'city', 'dealer', 'media']);
    
    // Cache for future requests
    RedisService::cacheCar($car->id, $car, 3600);
    
    return view('cars.show', compact('car'));
}
```

### Example: Caching Search Results

```php
use App\Services\RedisService;

public function index(Request $request)
{
    $query = $request->input('q');
    
    if ($query) {
        // Check cache first
        $cachedResults = RedisService::getCachedSearchResults($query);
        
        if ($cachedResults) {
            return $cachedResults;
        }
        
        // Perform search
        $results = $this->searchService->search($request);
        
        // Cache results
        RedisService::cacheSearchResults($query, $results, 600);
        
        return $results;
    }
    
    // ... rest of the code
}
```

## Cache Tags (Advanced)

For better cache management, you can use cache tags:

```php
use Illuminate\Support\Facades\Cache;

// Store with tags
Cache::tags(['cars', 'featured'])->put('key', $data, 3600);

// Clear by tag
Cache::tags(['cars'])->flush();
```

## Monitoring

### Check Redis Health

```php
use App\Services\RedisService;

$health = RedisService::healthCheck();
// Returns: ['status' => 'healthy', 'connected' => true]
```

### Redis CLI Commands

```bash
# Connect to Redis CLI
redis-cli

# View all keys
KEYS *

# Get value
GET key_name

# View memory usage
INFO memory

# Monitor commands in real-time
MONITOR
```

## Performance Tips

1. **Set appropriate TTL**: Don't cache data forever
2. **Use cache tags**: For easier cache invalidation
3. **Monitor memory**: Redis stores data in memory
4. **Use pipelines**: For bulk operations
5. **Enable persistence**: Configure Redis to save data to disk

## Troubleshooting

### Redis Connection Failed

1. Check if Redis server is running: `redis-cli ping`
2. Verify host and port in `.env`
3. Check firewall settings
4. Verify Redis password if set

### Cache Not Working

1. Clear config cache: `php artisan config:clear`
2. Clear application cache: `php artisan cache:clear`
3. Check Redis connection: `php artisan tinker` then `Redis::ping()`

## Queue Integration

To use Redis for queues, update `.env`:

```env
QUEUE_CONNECTION=redis
```

Then run queue worker:

```bash
php artisan queue:work redis
```

## Session Storage

Sessions are automatically stored in Redis when `SESSION_DRIVER=redis` is set.

## Broadcasting

Redis can be used for broadcasting events:

```php
// In Event class
public function broadcastOn()
{
    return new Channel('car-status-updates');
}
```

## Best Practices

1. Always set TTL for cached data
2. Use descriptive cache keys
3. Clear cache when data is updated
4. Monitor Redis memory usage
5. Use Redis for frequently accessed data
6. Don't cache sensitive data without encryption

