<?php

namespace notes\modules\v1\controllers;

use notes\components\BehaviorsTrait;
use notes\modules\v1\models\User;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    use BehaviorsTrait;

    public function actionIndex()
    {
        $provider = User::findAllAsProvider();
        return $provider;
    }

    public function actionView(int $id)
    {
        $model = User::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("User $id not found");
        }
        return $model;
    }
}
