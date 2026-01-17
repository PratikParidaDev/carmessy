<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Services\RedisService;

class TestRedisConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Redis connection and functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Redis Connection...');
        $this->newLine();

        // Test basic connection
        try {
            $result = Redis::ping();
            if ($result === 'PONG' || $result === true) {
                $this->info('✓ Redis connection successful!');
            } else {
                $this->error('✗ Redis connection failed - Unexpected response: ' . $result);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Redis connection failed: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Make sure Redis server is running:');
            $this->line('  - Linux/macOS: redis-server');
            $this->line('  - Windows: redis-server (or use WSL)');
            $this->line('  - Check .env file for REDIS_HOST and REDIS_PORT');
            return Command::FAILURE;
        }

        $this->newLine();

        // Test write/read
        $this->info('Testing write/read operations...');
        try {
            $testKey = 'test:connection:' . time();
            $testValue = 'Hello Redis!';
            
            Redis::set($testKey, $testValue);
            $retrieved = Redis::get($testKey);
            
            if ($retrieved === $testValue) {
                $this->info('✓ Write/Read test successful!');
                Redis::del($testKey);
            } else {
                $this->error('✗ Write/Read test failed');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ Write/Read test failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // Test RedisService
        $this->info('Testing RedisService...');
        try {
            $health = RedisService::healthCheck();
            if ($health['connected']) {
                $this->info('✓ RedisService is working!');
            } else {
                $this->error('✗ RedisService health check failed');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('✗ RedisService test failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();

        // Display Redis info
        $this->info('Redis Information:');
        try {
            $info = Redis::info();
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Redis Version', $info['redis_version'] ?? 'N/A'],
                    ['Used Memory', $this->formatBytes($info['used_memory'] ?? 0)],
                    ['Connected Clients', $info['connected_clients'] ?? 'N/A'],
                    ['Total Commands Processed', $info['total_commands_processed'] ?? 'N/A'],
                ]
            );
        } catch (\Exception $e) {
            $this->warn('Could not retrieve Redis info: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('✓ All Redis tests passed!');
        
        return Command::SUCCESS;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
