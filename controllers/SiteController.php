<?php

namespace notes\controllers;

use notes\components\BehaviorsTrait;
use yii\rest\Controller;

class SiteController extends Controller
{
    use BehaviorsTrait;

    public function actionIndex()
    {
        return [
            'title' => 'REST-API for Notes Management Tool',
            'info' => 'You need an appropriate client to access this API',
            'github' => 'https://github.com/tbreuss/notes-client',
            'url' => 'https://notes.tebe.ch',
        ];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        return [
            'name' => 'Not found exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'status' => 404
        ];
    }
}
