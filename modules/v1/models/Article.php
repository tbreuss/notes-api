<?php

namespace app\modules\v1\models;

use app\models\History;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

/**
 * Class Article
 * @package app\modules\v1\models
 * @property int id
 * @property string title
 * @property string content
 * @property string tag_ids
 * @property int views
 * @property int likes
 * @property string created
 * @property int created_by
 * @property string modified
 * @property int modified_by
 */
class Article extends ActiveRecord
{
    public $tags;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{articles}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['title', 'content', 'tags'], 'required'],
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        $fields['tags'] = 'tags';
        $fields['created_by_user'] = 'createdByUser';
        $fields['modified_by_user'] = 'modifiedByUser';
        return $fields;
    }

    /**
     * @return array
     */
    public static function findLatestItems()
    {
        $articles = static::find()
            ->select(['id', 'title', 'created'])
            ->limit(5)
            ->orderBy('created DESC')
            ->asArray()
            ->all();
        return $articles;
    }

    /**
     * @return array
     */
    public static function findMostLikedItems()
    {
        $articles = static::find()
            ->select(['id', 'title', 'likes'])
            ->limit(5)
            ->orderBy('likes DESC')
            ->asArray()
            ->all();
        return $articles;
    }

    /**
     * @return array
     */
    public static function findLastModifiedItems()
    {
        $articles = static::find()
            ->select(['id', 'title', 'modified'])
            ->limit(5)
            ->orderBy('modified DESC')
            ->asArray()
            ->all();
        return $articles;
    }

    /**
     * @return array
     */
    public static function findPopularItems()
    {
        $articles = static::find()
            ->select(['id', 'title', 'views'])
            ->limit(5)
            ->orderBy('views DESC')
            ->asArray()
            ->all();
        return $articles;
    }

    /**
     * @return void
     */
    public function loadTagNames()
    {
        $tags = Tag::find()
            ->select(['id', 'name'])
            ->where(['in', 'id', explode(',', $this->tag_ids)])
            ->orderBy('name DESC')
            ->asArray()
            ->all();

        foreach ($tags as $tag) {
            $this->tags[] = $tag['name'];
        }
    }

    /**
     * @param string $q
     * @param array $tags
     * @return ActiveDataProvider
     */
    public static function findAllAsProvider($q = '', array $tags = [])
    {
        $query = new Query;

        $query->select('a.id, a.title AS title, GROUP_CONCAT(t.name) AS tags, a.created, a.modified, a.views');
        $query->from('articles a');
        $query->innerJoin('tags t', 'FIND_IN_SET(t.id, a.tag_ids)>0');
        $query->groupBy('a.id');

        if (!empty($q)) {
            $query->andWhere('(a.title LIKE :q OR a.content LIKE :q)', ['q' => '%' . $q . '%']);
        }

        if (!empty($tags)) {
            foreach ($tags as $i => $tag) {
                $key = 'tag_id_' . $i;
                $query->andWhere("FIND_IN_SET(:${key}, a.tag_ids)>0", [$key => $tag]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
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

        // TODO optimize implementation to explode tags
        $models = $dataProvider->getModels();
        foreach ($models as $i => $model) {
            $model['tags'] = explode(',', $model['tags']);
            $models[$i] = $model;
        }
        $dataProvider->setModels($models);

        return $dataProvider;
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getModifiedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'modified_by']);
    }

    /**
     * @param string $tags
     * @param int $articleId
     * @param int $userId
     * @param array $oldTagIds
     * @throws ServerErrorHttpException
     * @throws Exception
     */
    private function saveTags(string $tags, int $articleId, int $userId, array $oldTagIds)
    {
        // Tags in Tabelle speichern
        $tagIds = Tag::saveAll($tags, $userId);

        // Tag-IDs in Zwischentabelle speichern
        ArticleTag::saveTags($articleId, $tagIds);

        // Tag-IDs in Artikel aktualisieren
        Article::updateAll(['tag_ids' => implode(',', $tagIds)], ['id' => $articleId]);

        // Counter in Tags aktualisieren
        $oldAndNewTagIds = array_merge($tagIds, $oldTagIds);
        Tag::updateFrequencies($oldAndNewTagIds);

        // Tags mit Counter=0 entfernen
        Tag::deleteAll('frequency <= 0');
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->tag_ids = ''; // we handle this in afterSave
            if ($this->isNewRecord) {
                $this->created = new Expression('NOW()');
                $this->created_by = \Yii::$app->user->id;
            } else {
                $this->modified = new Expression('NOW()');
                $this->modified_by = \Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws ServerErrorHttpException
     */
    public function afterSave($insert, $changedAttributes)
    {
        $oldTagIds = [];
        if (!empty($changedAttributes['tag_ids'])) {
            $oldTagIds = explode(',', $changedAttributes['tag_ids']);
        }

        $userId = \Yii::$app->user->id;
        $this->saveTags($this->tags, $this->id, $userId, $oldTagIds);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $articleTags = ArticleTag::findAll(['article_id' => $this->id]);
        $tagIds = ArrayHelper::getColumn($articleTags, 'tag_id');

        ArticleLike::deleteAll(['article_id' => $this->id]);
        ArticleTag::deleteAll(['article_id' => $this->id]);
        ArticleView::deleteAll(['article_id' => $this->id]);
        History::deleteAll(['article_id' => $this->id]);
        Tag::updateFrequencies($tagIds);
        Tag::deleteAll('frequency <= 0');

        return true;
    }

    public function updateViews()
    {
        // Limit to one view per day
        $model = ArticleView::find()->where([
            'article_id' => $this->id,
            'user_id' => \Yii::$app->user->id,
            'created' => date('Y-m-d')
        ])->one();

        if (empty($model)) {
            // Insert into relation table
            $model = new ArticleView();
            $model->article_id = $this->id;
            $model->user_id = \Yii::$app->user->id;
            $model->insert();

            // Update counter for quick access
            $this->updateCounters(['views' => 1]);
        }
    }
}
