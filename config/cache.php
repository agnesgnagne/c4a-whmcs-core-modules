<?php
return [
    'redis' => [
        'host' => getenv('REDIS_SERVER') ?: '127.0.0.1',
        'port' => (int)(getenv('REDIS_PORT') ?: 6379),
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'database' => (int)(getenv('REDIS_DB') ?: 0),
        'timeout' => 2.0,
    ],
    'cache' => [
        //'namespace' => 'whmcs_mon_module_v1',
        'default_ttl' => 300,
        //'fallback_to_fs' => true, // Swicth to filesystem if Redis is down
        'fs_cache_dir' => getenv('REDIS_FS_CACHE') ?: __DIR__ . '/../cache',
    ],
];