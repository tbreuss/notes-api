<?php

namespace app\components;

use yii\rest\OptionsAction;

trait ActionsTrait
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class,
            ],
        ];
    }
}
