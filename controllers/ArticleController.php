<?php

namespace notes\controllers;

use notes\components\BehaviorsTrait;
use notes\models\Article;
use yii\rest\ActiveController;

class ArticleController extends ActiveController
{
    use BehaviorsTrait;

    public $modelClass = 'notes\models\Article';

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
}
