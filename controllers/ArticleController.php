<?php

namespace notes\controllers;

use notes\components\RestController;
use notes\models\Article;

class ArticleController extends RestController
{
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
