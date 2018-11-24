<?php

namespace notes\modules\v1\models;

use Firebase\JWT\JWT;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package notes\models
 * @see https://stackoverflow.com/questions/25327476/implementing-an-restful-api-authentication-using-tokens-yii-yii2
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return '{{users}}';
    }

    public function fields()
    {
        $fields = parent::fields();

        // remove fields that contain sensitive information
        unset(
            $fields['password'],
            $fields['salt'],
            $fields['access_token']
        );

        return $fields;
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token,
            'deleted' => 0
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function generateToken(): string
    {
        $payload = array(
            "iss" => 'ch.tebe.notes',
            "iat" => time(),
            //"exp" => time() + (60*60*24),
            'user' => [
                'id' => $this->id,
                'name' => $this->username,
                'role' => $this->role,
                'scopes' => json_decode($this->scopes, true)
            ]
        );
        $key = Yii::app()->params['jwt.private_key'];
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'deleted' => 0
        ]);
    }

    function validatePassword(string $password): bool
    {
        return $this->hashPassword($password, $this->salt) === $this->password;
    }

    public function hashPassword(string $password, string $salt): string
    {
        return md5($salt . $password);
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
