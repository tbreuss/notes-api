<?php

namespace app\modules\v1\controllers;

use app\components\ActionsTrait;
use app\components\BehaviorsTrait;
use app\models\User;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    use ActionsTrait;
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
