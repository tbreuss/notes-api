<?php

namespace notes\modules\v1\models;

use yii\db\ActiveRecord;

/**
 * Class ArticleLike
 * @package notes\modules\v1\models
 * @property int article_id
 * @property int tag_id
 */
class ArticleLike extends ActiveRecord
{
    public static function tableName()
    {
        return '{{article_likes}}';
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
