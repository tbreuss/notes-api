<?php

namespace notes\controllers;

use notes\components\RestController;

class PingController extends RestController
{
    public function actionIndex()
    {
        return [
            'name' => 'ch.tebe.notes',
            'time' => date('c'),
            'version' => '0.5'
        ];
    }
}
