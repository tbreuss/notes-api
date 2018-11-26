<?php

namespace notes\modules\v1\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class Article
 * @package notes\modules\v1\models
 * @property int id
 * @property string title
 * @property string content
 * @property string tag_ids
 * @property int views
 * @property int likes
 * @property string created
 * @property string modified
 */
class Article extends ActiveRecord
{
    public $tags;

    public static function tableName()
    {
        return '{{articles}}';
    }

    public function rules()
    {
        return [
            [['title', 'content', 'tags'], 'required'],
        ];
    }

    public function fields()
    {
        $fields = parent::fields();
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

    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getModifiedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'modified_by']);
    }

    public static function findAllAsProvider($q = '', array $tags = [])
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

    private function saveTags(string $tags, int $articleId, int $userId, array $oldTagIds)
    {
        // Tags in Tabelle speichern
        $tagIds = Tag::saveAll($tags, $userId);

        // Tag-IDs in Zwischentabelle speichern
        ArticleTag::saveTags($articleId, $tagIds);

        // Tag-IDs in Artikel aktualisieren
        Article::updateAll(['tag_ids' => implode(',', $tagIds)], ['id' => $articleId]);

        // Counter in Tags aktualisieren
        Tag::updateFrequencies(array_merge($tagIds, $oldTagIds));

        // Tags mit Counter=0 entfernen
        Tag::deleteAll('frequency <= 0');
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->tag_ids = ''; // we handle this in afterSave
            if ($this->isNewRecord) {
                $this->created = new Expression('NOW()');
            } else {
                $this->modified = new Expression('NOW()');
            }
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $oldTagIds = [];
        if (!empty($changedAttributes['tag_ids'])) {
            $oldTagIds = explode(',', $changedAttributes['tag_ids']);
        }

        $this->saveTags($this->tags, $this->id, 99, $oldTagIds);
    }

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
}
