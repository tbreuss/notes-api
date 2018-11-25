<?php

namespace notes\modules\v1\models;

use yii\db\ActiveRecord;

/**
 * Class ArticleView
 * @package notes\modules\v1\models
 * @property int article_id
 * @property int tag_id
 */
class ArticleView extends ActiveRecord
{
    public static function tableName()
    {
        return '{{article_views}}';
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
