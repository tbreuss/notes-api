<?php

namespace notes\modules\v1\models;

use yii\data\ActiveDataProvider;
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

    /**
     * @return ActiveDataProvider
     */
    public static function findAllAsProvider()
    {
        $provider = new ActiveDataProvider([
            'query' => static::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $provider;
    }
}
