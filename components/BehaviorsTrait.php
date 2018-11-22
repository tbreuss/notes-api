<?php

namespace notes\components;

use yii\filters\auth\HttpBearerAuth;

trait BehaviorsTrait
{
    public function behaviors()
    {
        // remove rateLimiter which requires an authenticated user to work
        $behaviors = parent::behaviors();
        unset($behaviors['rateLimiter']);
        /*
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        */
        return $behaviors;
    }
}
