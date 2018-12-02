<?php

namespace notes\modules\v1\controllers;

use notes\components\ActionsTrait;
use notes\components\BehaviorsTrait;
use notes\models\User;
use yii\rest\Controller;

class SiteController extends Controller
{
    use ActionsTrait;
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
            $model->lastlogin = date('Y-m-d H:i:s');
            $model->save(false);
            $token = $model->generateAccessToken();
            return $token;
        }
        \Yii::$app->getResponse()->setStatusCode(422);
        return [
            'username' => 'Benutzername/Passwort falsch',
            'password' => 'Benutzername/Passwort falsch'
        ];
    }
}
