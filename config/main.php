<?php

return [
    'id' => 'ch.tebe.notes',
    // the basePath of the application will be the `micro-app` directory
    'basePath' => dirname(__DIR__),
    // this is where the application will find all controllers
    'controllerNamespace' => 'notes\controllers',
    // set an alias to enable autoloading of classes from the 'micro' namespace
    'aliases' => [
        '@notes' => dirname(__DIR__),
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'errorHandler' => [
            'errorAction' => 'error/index',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => true,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'ping',
                    'extraPatterns' => ['GET' => 'index'],
                    'pluralize' => false
                ],
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
    ]
];
