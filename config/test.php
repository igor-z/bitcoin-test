<?php

use app\components\bitcoinRpc\Client;
use app\repositories\BitcoinRepository;
use app\services\BitcoinService;
use yii\di\Instance;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';
$bitcoinRpcClient = require __DIR__.'/bitcoin-rpc-client.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'container' => [
        'definitions' => [
            'app\services\BitcoinRepositoryInterface' => [
                ['class' => BitcoinRepository::class],
                [Instance::of('app\components\bitcoinRpc\ClientInterface')],
            ],
            'app\services\BitcoinServiceInterface' => [
                ['class' => BitcoinService::class],
                [Instance::of('app\services\BitcoinRepositoryInterface')],
            ],
            'app\components\bitcoinRpc\ClientInterface' => function ($container, $params, $config) use($bitcoinRpcClient) {
                return new Client(
                    $bitcoinRpcClient['user'],
                    $bitcoinRpcClient['password'],
                    $bitcoinRpcClient['host'],
                    $bitcoinRpcClient['port']
                );
            },
        ],
    ],
    'components' => [
        'db' => $db,
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],
    ],
    'params' => $params,
];
