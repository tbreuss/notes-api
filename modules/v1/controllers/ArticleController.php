<?php

namespace notes\modules\v1\controllers;

use notes\components\BehaviorsTrait;
use notes\modules\v1\models\Article;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class ArticleController extends Controller
{
    use BehaviorsTrait;

    public function actionIndex($q = '', array $tags = [])
    {
        $provider = Article::findAllAsProvider($q, $tags);
        return $provider;
    }

    public function actionCreate()
    {
        $model = new Article();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $response->getHeaders()->set('Location', Url::toRoute(['article/view', 'id' => $model->id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create article for unknown reason.');
        }
        return $model;
    }

    public function actionDelete(int $id)
    {
        $model = Article::findOne($id);
        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete article for unknown reason.');
        }
        Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionView(int $id)
    {
        $model = Article::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Article $id not found");
        }
        $model->updateCounters(['views' => 1]);
        return $model;
    }

    public function actionUpdate(int $id)
    {
        $model = Article::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Article $id not found");
        }
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update article for unknown reason.');
        }
        return $model;
    }

    public function actionLatest()
    {
        return Article::findLatestItems();
    }

    public function actionLiked()
    {
        return Article::findMostLikedItems();
    }

    public function actionModified()
    {
        return Article::findLastModifiedItems();
    }

    public function actionPopular()
    {
        return Article::findPopularItems();
    }

    public function actionUpload()
    {
    }

    public function actionSelectedtags()
    {
    }
}
