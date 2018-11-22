<?php

namespace notes\controllers;

use notes\components\BehaviorsTrait;
use notes\models\Article;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\rest\ActiveController;

class ArticleController extends ActiveController
{
    use BehaviorsTrait;

    public $modelClass = 'notes\models\Article';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex($q = '', array $tags = [])
    {
        $query = new Query;

        $query->select('a.id, a.title AS title, GROUP_CONCAT(t.name) AS tags, a.created, a.modified, a.views');
        $query->from('articles a');
        $query->innerJoin('tags t', 'FIND_IN_SET(t.id, a.tag_ids)');
        $query->groupBy('a.id');

        if (!empty($q)) {
            $query->andWhere('(a.title LIKE :q OR a.content LIKE :q)', ['q' => '%' . $q . '%']);
        }

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $query->andWhere('FIND_IN_SET(:tag_id, a.tag_ids)>0', ['tag_id' => $tag]);
            }
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'title',
                    'changed' => [
                        'asc' => ['a.modified' => SORT_DESC, 'a.title' => SORT_ASC],
                        'desc' => ['a.modified' => SORT_ASC, 'a.title' => SORT_ASC],
                        'default' => SORT_DESC
                    ],
                    'created' => [
                        'asc' => ['a.created' => SORT_DESC, 'a.title' => SORT_ASC],
                        'desc' => ['a.created' => SORT_ASC, 'a.title' => SORT_ASC],
                        'default' => SORT_DESC
                    ],
                    'popular' => [
                        'asc' => ['a.views' => SORT_DESC, 'a.title' => SORT_ASC],
                        'desc' => ['a.views' => SORT_ASC, 'a.title' => SORT_ASC],
                        'default' => SORT_DESC
                    ]
                ],
                'defaultOrder' => [
                    'title' => SORT_ASC
                ]
            ]
        ]);
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
