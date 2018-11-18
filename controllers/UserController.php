<?php

namespace notes\controllers;

use notes\components\RestController;

class UserController extends RestController
{
    public $modelClass = 'notes\models\User';
}
