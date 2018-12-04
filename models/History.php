<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class History
 * @package app\modules\v1\models
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
