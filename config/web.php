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
    'modules' => [
        'v1' => [
            'class' => 'notes\modules\v1\Module',
        ]
    ],
    'components' => [
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
                // site
                'GET /' => 'site/index',
                'GET v1/ping' => 'v1/site/ping',
                'POST v1/login' => 'v1/site/login',
                // articles
                'GET v1/articles/<id:\d+>' => 'v1/article/view',
                'PUT v1/articles/<id:\d+>' => 'v1/article/update',
                'GET v1/articles' => 'v1/article/index',
                'POST v1/articles' => 'v1/article/create',
                'GET v1/articles/latest' => 'v1/article/latest',
                'GET v1/articles/liked' => 'v1/article/liked',
                'GET v1/articles/modified' => 'v1/article/modified',
                'GET v1/articles/popular' => 'v1/article/popular',
                'POST v1/articles/upload' => 'v1/article/upload',
                // users
                'GET v1/users/<id:\d+>' => 'v1/user/view',
                'GET v1/users' => 'v1/user/index',
                // tags
                'GET v1/tags/<id:\d+>' => 'v1/tag/view',
                'GET v1/tags' => 'v1/tag/index',
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
