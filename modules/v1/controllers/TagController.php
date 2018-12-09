<?php

namespace app\modules\v1\controllers;

use app\components\ActionsTrait;
use app\modules\v1\models\Tag;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

class TagController extends Controller
{
    use ActionsTrait;

    public function actionIndex()
    {
        $provider = Tag::findAllAsProvider();
        return $provider;
    }

    public function actionView(int $id)
    {
        $model = Tag::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Tag {$id} not found");
        }
        return $model;
    }

    public function actionSelected($q = '', array $tags = [])
    {
        return Tag::findAllSelected($q, $tags);
    }
}
