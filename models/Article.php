<?php

namespace notes\models;

use yii\db\ActiveRecord;

class Article extends ActiveRecord
{
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
        #$fields[] = 'createdByUser';
        #$fields[] = 'modifiedByUser';
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
}
