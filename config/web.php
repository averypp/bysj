<?php

$params = require(__DIR__ . '/params.php');


$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'timeZone'=>'Asia/Shanghai',//这里设置时区@echo
    'defaultRoute' => 'site/index',//index.php 默认跳转显示的页面
    'components' => [
        'myHelp' =>[
            'class' => 'app\libraries\MyHelper',
        ],
        'simpleValidator' =>[
            'class' => 'app\assets\util\SimpleValidator',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'R-AYxmmGyACs_aFphnvqZsBUKm_l3tkg',
        ],
        /*'cache' => [
            'class' => 'yii\caching\FileCache',
        ],*/
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => '127.0.0.1',
                'port' => 6379,
                'database' => 1,
            ],
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            // 'loginUrl' => ['r=site/login'],
            'identityCookie' => ['name' => 'sea_identity'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'/*,'info','trace'*/],
                    //'categories' => ['app\controllers\ProductController*'],
                    'logFile' => '@app/runtime/logs/yii.log',
                    'maxFileSize' => 1024 * 1024,
                    'maxLogFiles' => 1,
                ],
                /*'email' => [
                    'class' => 'yii\log\EmailTarget',
                    'levels' => ['error'],
                    'categories' => ['yii\db\*'],
                    'message' => [
                        'to' => ['604497732@qq.com'],
                        'subject' => '来自 example.com 的新日志消息',
                    ],
                ],*/
            ],
        ],
        /*'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],*/
        'db' => require(__DIR__ . '/db.php'),
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
