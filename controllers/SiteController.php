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
            'name' => 'ch.tebe.notes'
        ];
    }

    public function actionPing()
    {
        return [
            'name' => 'ch.tebe.notes',
            'time' => date('c'),
            'version' => '0.5'
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
