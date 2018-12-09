<?php

namespace app\modules\v1;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        $app->getUrlManager()->addRules([
            'GET v1/ping' => 'v1/site/ping',
            'POST v1/login' => 'v1/site/login',
            'v1/ping' => 'v1/site/options',
            'v1/login' => 'v1/site/options',

            // articles
            'GET v1/articles/<id:\d+>' => 'v1/article/view',
            'PUT v1/articles/<id:\d+>' => 'v1/article/update',
            'DELETE v1/articles/<id:\d+>' => 'v1/article/delete',
            'GET v1/articles' => 'v1/article/index',
            'POST v1/articles' => 'v1/article/create',
            'GET v1/articles/latest' => 'v1/article/latest',
            'GET v1/articles/liked' => 'v1/article/liked',
            'GET v1/articles/modified' => 'v1/article/modified',
            'GET v1/articles/popular' => 'v1/article/popular',
            'POST v1/articles/upload' => 'v1/article/upload',
            'v1/articles/<id:\d+>' => 'v1/article/options',
            'v1/articles' => 'v1/article/options',
            'v1/articles/latest' => 'v1/article/options',
            'v1/articles/liked' => 'v1/article/options',
            'v1/articles/modified' => 'v1/article/options',
            'v1/articles/popular' => 'v1/article/options',
            'v1/articles/upload' => 'v1/article/options',

            // users
            'GET v1/users/<id:\d+>' => 'v1/user/view',
            'GET v1/users' => 'v1/user/index',
            'v1/users/<id:\d+>' => 'v1/user/options',
            'v1/users' => 'v1/user/options',

            // tags
            'GET v1/tags/<id:\d+>' => 'v1/tag/view',
            'GET v1/tags' => 'v1/tag/index',
            'GET v1/tags/selected' => 'v1/tag/selected',
            'v1/tags/<id:\d+>' => 'v1/tag/options',
            'v1/tags' => 'v1/tag/options',
            'v1/tags/selected' => 'v1/tag/options',

        ], false);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove rateLimiter which requires an authenticated user to work
        unset($behaviors['rateLimiter']);

        // remove authentication filter
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
                'site/*',
                'article/options',
                'tag/options',
                'user/options',
            ]
        ];

        return $behaviors;
    }
}
