<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

return [
    'id' => 'ch.tebe.notes',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'notes\controllers',
    'aliases' => [
        '@notes' => dirname(__DIR__),
    ],
    'bootstrap' => [
        'log',
        'v1'
    ],
    'modules' => [
        'v1' => [
            'class' => 'notes\modules\v1\Module',
        ]
    ],
    'components' => [
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                /*'db' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['profile'],
                ],*/
            ],
        ],
        'db' => $db,
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => 'ZCxc233xJDfhOnRydWV9wZeuweG9lIiwiYWRtaW4i',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser'
            ]
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => true,
            'rules' => [
                'GET /' => 'site/index'
            ],
        ],
        'user' => [
            'identityClass' => 'notes\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false
        ],
        /*'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
        ]*/
    ],
    'params' => $params
];
