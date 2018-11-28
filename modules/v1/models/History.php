<?php

namespace notes\modules\v1\models;

use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\ServerErrorHttpException;

/**
 * Class History
 * @package notes\modules\v1\models
 */
class History extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{history}}';
    }
}
