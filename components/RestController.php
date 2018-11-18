<?php

namespace notes\components;

use yii\rest\ActiveController;

class RestController extends ActiveController
{
    public function behaviors()
    {
        // remove rateLimiter which requires an authenticated user to work
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }
}
