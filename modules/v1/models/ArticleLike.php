<?php

namespace app\modules\v1\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Class ArticleLike
 * @package app\modules\v1\models
 * @property int article_id
 * @property int tag_id
 * @property string created
 */
class ArticleLike extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{article_likes}}';
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created = new Expression('NOW()');
            return true;
        }
        return false;
    }
}
