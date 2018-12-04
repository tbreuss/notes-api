<?php

namespace app\modules\v1\controllers;

use app\components\ActionsTrait;
use app\components\BehaviorsTrait;
use app\modules\v1\models\Article;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

class ArticleController extends Controller
{
    use ActionsTrait;
    use BehaviorsTrait;

    public function actionIndex($q = '', array $tags = [])
    {
        $provider = Article::findAllAsProvider($q, $tags);
        return $provider;
    }

    public function actionCreate()
    {
        $model = new Article();
        $data = \Yii::$app->getRequest()->getBodyParams();
        $model->load($data, '');
        if ($model->insert()) {
            $response = \Yii::$app->getResponse();
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
        if (!$model) {
            throw new NotFoundHttpException("Article $id not found");
        }
        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete article for unknown reason.');
        }
        \Yii::$app->getResponse()->setStatusCode(204);
    }

    public function actionView(int $id)
    {
        $model = Article::find()
            ->with('createdByUser', 'modifiedByUser')
            ->where(['id' => $id])
            ->one();
        if (!$model) {
            throw new NotFoundHttpException("Article $id not found");
        }
        $model->loadTagNames();
        $model->updateCounters(['views' => 1]);
        return $model;
    }

    public function actionUpdate(int $id)
    {
        $model = Article::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException("Article $id not found");
        }
        $data = \Yii::$app->getRequest()->getBodyParams();
        $model->load($data, '');
        if ($model->update() === false && !$model->hasErrors()) {
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
        $userId = \Yii::$app->user->id;

        $file = UploadedFile::getInstanceByName('file');

        if (empty($file)) {
            throw new BadRequestHttpException('File not uploaded');
        }

        $fileEndings = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/gif' => 'gif',
            'image/png' => 'png'
        ];

        if ($file->error > 0) {
            throw new BadRequestHttpException('An error occured');
        }

        if (!in_array($file->type, array_keys($fileEndings))) {
            throw new BadRequestHttpException('Invalid file type');
        }

        if ($file->size > 1000000) {
            throw new BadRequestHttpException('File size to big');
        }

        $basename = md5_file($file->tempName);
        if ($basename === false) {
            throw new ServerErrorHttpException('Could not create md5 sum from file');
        }

        // determine path and url
        $relPath = sprintf('media/%s/%s.%s', $userId, $basename, $fileEndings[$file->type]);
        $absFilePath = \Yii::getAlias('@webroot/' . $relPath);
        $fileUrl = \Yii::getAlias('@web/' . $relPath);

        // create dir if not exists
        $directory = dirname($absFilePath);
        if (!is_dir($directory)) {
            FileHelper::createDirectory($directory, 0775, true);
        }

        if (!move_uploaded_file($file->tempName, $absFilePath)) {
            throw new ServerErrorHttpException('Could not move uploaded file');
        }

        return [
            'name' => $file->name,
            'location' => $fileUrl
        ];
    }
}
