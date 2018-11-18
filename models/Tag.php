<?php

namespace notes\models;

use yii\db\ActiveRecord;

class Tag extends ActiveRecord
{
    public static function tableName()
    {
        return '{{tags}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
        ];
    }
}
