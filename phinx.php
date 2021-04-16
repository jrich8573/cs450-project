<?php

$db_config = (require 'api/app/config.php')['db'];

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => getenv('PIPELINE_STAGE') ?? 'development',
        'production' => $db_config,
        'staging' => $db_config,
        'development' => $db_config,
        'testing' => array_combine(
            array_keys($db_config),
            array_map(function($k, $v) {
                return $k == 'host' ? $v . '_for_tests' : $v;
            }, array_keys($db_config), $db_config)
        ),
    ],
    'version_order' => 'creation'
];
