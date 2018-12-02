<?php

use app\components\bitcoinRpc\Client;
use app\repositories\BitcoinRepository;
use app\services\BitcoinService;
use yii\di\Instance;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$bitcoinRpcClient = require __DIR__.'/bitcoin-rpc-client.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
	'container' => [
		'definitions' => [
			'bitcoinRepository' => [
				['class' => BitcoinRepository::class],
				[Instance::of('bitcoinRpcClient')],
			],
			'app\services\BitcoinService' => [
				['class' => BitcoinService::class],
				[Instance::of('bitcoinRepository')],
			],
		],
		'singletons' => [
			'bitcoinRpcClient' => function ($container, $params, $config) use($bitcoinRpcClient) {
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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'zmWBZ26hdOkjAdVen9CIIEfSsNZpWhD9',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
