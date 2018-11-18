<?php

namespace notes\controllers;

use notes\components\RestController;

class ErrorController extends RestController
{
    public function actionIndex()
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
