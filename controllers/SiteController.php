<?php

namespace app\controllers;

use yii\rest\Controller;

class SiteController extends Controller
{

    public function actionIndex()
    {
        return [
            'title' => 'REST-API for Notes Management Tool',
            'info' => 'You need an appropriate client to access this API',
            'links' => [
                'github' => 'https://github.com/tbreuss/notes-client',
                'website' => 'https://notes.tebe.ch'
            ]
        ];
    }

    public function actionError()
    {
        $exception = \Yii::$app->errorHandler->exception;
        return [
            'name' => 'Error Action',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'status' => $exception->statusCode,
            'type' => get_class($exception)
        ];
    }
}
