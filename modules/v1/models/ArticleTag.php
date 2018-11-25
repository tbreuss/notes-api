<?php

namespace notes\modules\v1\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\ServerErrorHttpException;

/**
 * Class ArticleTag
 * @package notes\modules\v1\models
 * @property int article_id
 * @property int tag_id
 * @property string created
 */
class ArticleTag extends ActiveRecord
{
    public static function tableName()
    {
        return '{{article_to_tag}}';
    }

    /**
     * @param int $articleId
     * @param int[] $tagIds
     * @throws ServerErrorHttpException
     */
    public static function saveTags(int $articleId, array $tagIds)
    {
        static::deleteAll(
            'article_id = :articleId AND tag_id NOT IN (:tagIds)',
            [
            'articleId' => $articleId,
            'tagIds' => $tagIds
            ]
        );

        foreach ($tagIds as $tagId) {
            $model = static::findOne(['article_id' => $articleId, 'tag_id' => $tagId]);
            if (empty($model)) {
                $model = new static();
                $model->article_id = $articleId;
                $model->tag_id = $tagId;
                if (!$model->save()) {
                    throw new ServerErrorHttpException('Failed to save article_to_tags for unknown reason.');
                }
            }
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created = new Expression('NOW()');
            return true;
        }
        return false;
    }
}
