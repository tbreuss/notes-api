<?php

namespace notes\models;

use yii\db\ActiveRecord;

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
