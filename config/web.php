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
    'components' => [
        'db' => $db,
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => true,
            'rules' => [
                'GET /' => 'site/index',
                'GET ping' => 'site/ping',
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'article',
                    'extraPatterns' => [
                        'GET latest' => 'latest',
                        'GET liked' => 'liked',
                        'GET modified' => 'modified',
                        'GET popular' => 'popular'
                    ],
                    'except' => ['delete', 'create', 'update']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'user',
                    'except' => ['delete', 'create', 'update']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'tag',
                    'except' => ['delete', 'create', 'update']
                ],
            ],
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
