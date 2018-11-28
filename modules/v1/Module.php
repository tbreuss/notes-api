<?php

namespace notes\modules\v1;

use yii\base\Application;
use yii\base\BootstrapInterface;

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
            // users
            'GET v1/users/<id:\d+>' => 'v1/user/view',
            'GET v1/users' => 'v1/user/index',
            // tags
            'GET v1/tags/<id:\d+>' => 'v1/tag/view',
            'GET v1/tags' => 'v1/tag/index',
            'GET v1/tags/selected' => 'v1/tag/selected',
        ], false);
    }
}
