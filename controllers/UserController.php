<?php

namespace notes\controllers;

use notes\components\BehaviorsTrait;
use yii\rest\ActiveController;

class UserController extends ActiveController
{
    use BehaviorsTrait;

    public $modelClass = 'notes\models\User';
}
