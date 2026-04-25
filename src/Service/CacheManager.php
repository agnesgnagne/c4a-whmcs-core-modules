<?php

namespace WHMCS\Cloud4Africa\Service;

use Predis\Client as PredisClient;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ChainAdapter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use WHMCS\Cloud4Africa\Traits\LoggerTrait;

class CacheManager
{
    use LoggerTrait;
    
    /** @var array<string, self> mutli-instances */
    private static array $instances = [];
    
    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private CacheInterface $cache;
    
    /**
     * @var boolean
     */
    private bool $redisAvailable = false;
    
    /**
     * @param array $config
     * @param string $moduleNamespace
     */
    private function __construct(array $config, string $moduleNamespace)
    {
        $this->cache = $this->buildAdapter($config, $moduleNamespace);
    }
    
    /**
     * @param string $moduleNamespace
     * @return self
     */
    public static function getInstance(string $moduleNamespace): self
    {
        if (!isset(self::$instances[$moduleNamespace])) {
            $config = require __DIR__ . '/../../config/cache.php';
            self::$instances[$moduleNamespace] = new self($config, $moduleNamespace);
        }
        
        return self::$instances[$moduleNamespace];
    }
    
    /**
     * Reset all instances
     */
    public static function reset(): void
    {
        self::$instances = [];
    }
    
    /**
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function get(string $key, callable $callback, int $ttl = 300): mixed
    {
        return $this->adapter->get(
            $this->sanitizeKey($key),
            function (ItemInterface $item) use ($callback, $ttl): mixed {
                $result = $callback();
                
                if (is_array($result) && isset($result['__ttl'])) {
                    $item->expiresAfter((int) $result['__ttl']);
                    unset($result['__ttl']);
                } else {
                    $item->expiresAfter($ttl);
                }
                
                return $result;
            }
            );
    }
    
    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->cache->delete($this->sanitizeKey($key));
    }
    
    /**
     * @param string[] $keys
     */
    public function deleteMultiple(array $keys): void
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }
    
    /**
     * @return bool
     */
    public function clear(): bool
    {
        return $this->cache->clear();
    }
    
    /**
     * @return bool
     */
    public function isRedisAvailable(): bool
    {
        return $this->redisAvailable;
    }
    
    /**
     * @param string $moduleNamespace
     * @return string
     */
    private function buildNamespace(string $moduleNamespace): string
    {
        return 'whmcs_' . preg_replace('/[^a-z0-9_]/i', '_', $moduleNamespace);
    }
    
    /**
     * @param array $config
     * @param string $moduleNamespace
     * @return CacheInterface
     */
    private function buildAdapter(array $config, string $moduleNamespace): CacheInterface
    {
        $namespace = $this->buildNamespace($moduleNamespace);
        
//         if ($config['cache']['fallback_to_fs']) {
//             return $this->buildChainAdapter($config, $namespace);
//         }
        
        // Redis is required if fallback has not configured
        return new RedisAdapter(
            $this->createRedisClient($config['redis']),
            namespace: $namespace,
            defaultLifetime: $config['cache']['default_ttl']
        );
    }
    
    /**
     * @param array<string, mixed> $config
     * @param string $namespace
     * @return ChainAdapter|FilesystemAdapter
     */
    private function buildChainAdapter(array $config, string $namespace): ChainAdapter|FilesystemAdapter
    {
        $cacheConfig = $config['cache'];
        $adapters = [];
        
        try {
            $redis = $this->createRedisClient($config['redis']);
            $adapters[] = new RedisAdapter(
                $redis,
                namespace: $namespace,
                defaultLifetime: $cacheConfig['default_ttl']
                );
            $this->redisAvailable = true;
        } catch (\Throwable $e) {
            $this->log([
                'moduleName' => $namespace,
                'action' => __FUNCTION__,
                'request' => $cacheConfig,
                'response' => $e->getMessage(),
            ]);
        }
        
        // Filesystem instance by namespace
        $fsDir = rtrim($cacheConfig['fs_cache_dir'], '/') . '/' . $namespace;
        if (!is_dir($fsDir)) {
            mkdir($fsDir, 0755, true);
        }
        
        $adapters[] = new FilesystemAdapter(
            namespace: $namespace,
            defaultLifetime: $cacheConfig['default_ttl'],
            directory: $fsDir
        );
        
        if (count($adapters) === 1) {
            return $adapters[0];
        }
        
        return new ChainAdapter($adapters);
    }
    
    private function createRedisClient(array $config): PredisClient
    {
        $params = [
            'scheme' => 'tcp',
            'host' => $config['host'],
            'port' => $config['port'],
            'timeout' => $config['timeout'],
        ];
        
        if (!empty($config['password'])) {
            $params['password'] = $config['password'];
        }
        
        if ($config['database'] !== 0) {
            $params['database'] = $config['database'];
        }
        
        $client = new PredisClient($params);
        $client->ping();
        
        return $client;
    }
    
    private function sanitizeKey(string $key): string
    {
        return preg_replace('/[{}()\/\\\\@:]/', '_', $key);
    }
}
