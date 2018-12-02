<?php

namespace notes\components;

use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

trait BehaviorsTrait
{
    public function behaviors()
    {
        // @see https://www.yiiframework.com/doc/guide/2.0/en/rest-controllers

        // remove rateLimiter which requires an authenticated user to work
        $behaviors = parent::behaviors();

        // unset rate limiter filter
        unset($behaviors['rateLimiter']);

        // remove authentication filter
        $authenticator = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                #'Access-Control-Allow-Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                #'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Current-Page',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Per-Page',
                    'X-Pagination-Total-Count'
                ],
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => [
                'index',
                'ping',
                'login',
                'options'
            ]
        ];

        return $behaviors;
    }
}
