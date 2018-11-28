<?php

namespace notes\modules\v1\controllers;

use notes\components\BehaviorsTrait;
use notes\modules\v1\models\User;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

class SiteController extends Controller
{
    use BehaviorsTrait;

    public function actionPing()
    {
        return [
            'name' => 'ch.tebe.notes',
            'time' => date('c'),
            'version' => '0.5'
        ];
    }

    public function actionLogin()
    {
        $post = \Yii::$app->request->post();
        $model = User::findByUsername($post['username']);
        if (empty($model)) {
            \Yii::$app->getResponse()->setStatusCode(422);
            return [
                'username' => 'Benutzername/Passwort falsch',
                'password' => 'Benutzername/Passwort falsch'
            ];
        }
        if ($model->validatePassword($post["password"])) {
            $token = $model->generateToken();
            $model->lastlogin = date('Y-m-d H:i:s');
            $model->save(false);
            return $token;
        }
        \Yii::$app->getResponse()->setStatusCode(422);
        return [
            'username' => 'Benutzername/Passwort falsch',
            'password' => 'Benutzername/Passwort falsch'
        ];
    }
}
