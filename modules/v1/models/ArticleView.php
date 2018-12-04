<?php

namespace app\modules\v1\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Class ArticleView
 * @package app\modules\v1\models
 * @property int article_id
 * @property int tag_id
 * @property string created
 */
class ArticleView extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{article_views}}';
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
