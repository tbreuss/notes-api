<?php

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class UserController extends Controller
{
    /**
     * Creates a new user.
     * @param string $username
     * @param string $name
     * @param string $email
     * @param string $password
     * @return int
     */
    public function actionCreate(string $username, string $name, string $email, string $password)
    {
        $user = new User();

        $user->scenario = User::SCENARIO_CREATE;
        $user->username = $username;
        $user->password = $password;
        $user->name = $name;
        $user->email = $email;

        if ($user->save()) {
            return ExitCode::OK;
        }

        echo Console::errorSummary($user) . PHP_EOL;
        return ExitCode::UNSPECIFIED_ERROR;
    }

    /**
     * Renews password for an existing user.
     * @param string $username
     * @param string $password
     * @return int
     */
    public function actionPassword(string $username, string $password)
    {
        $user = User::findOne(['username' => $username]);
        if (empty($user)) {
            return ExitCode::NOUSER;
        }

        $user->scenario = User::SCENARIO_RENEW_PASSWORD;
        $user->password = $password;

        if ($user->save()) {
            return ExitCode::OK;
        }

        echo Console::errorSummary($user) . PHP_EOL;
        return ExitCode::UNSPECIFIED_ERROR;
    }
}
