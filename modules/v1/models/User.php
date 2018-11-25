<?php

namespace notes\modules\v1\models;

use Firebase\JWT\JWT;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * Class User
 * @package notes\models
 * @see https://stackoverflow.com/questions/25327476/implementing-an-restful-api-authentication-using-tokens-yii-yii2
 * @property int id
 * @property string username
 * @property string password
 * @property string salt
 * @property string name
 * @property string email
 * @property string role
 * @property string scopes
 * @property string article_views
 * @property string article_likes
 * @property string last_login
 * @property string created
 * @property string modified
 */
class User extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_RENEW_PASSWORD = 'renewPassword';

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

    public function rules()
    {
        return [
            // SCENARIO_CREATE
            // username
            [['username'], 'required', 'on' => self::SCENARIO_CREATE],
            [['username'], 'string', 'length' => [4, 50], 'on' => self::SCENARIO_CREATE],
            [['username'], 'unique', 'on' => self::SCENARIO_CREATE],
            // password
            [['password'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password'], 'string', 'min' => 8, 'on' => self::SCENARIO_CREATE],
            // name
            [['name'], 'required', 'on' => self::SCENARIO_CREATE],
            [['name'], 'string', 'length' => [2, 50], 'on' => self::SCENARIO_CREATE],
            // email
            [['email'], 'required', 'on' => self::SCENARIO_CREATE],
            [['email'], 'email', 'on' => self::SCENARIO_CREATE],
            // SCENARIO_RENEW_PASSWORD
            // password
            [['password'], 'required', 'on' => self::SCENARIO_RENEW_PASSWORD],
            [['password'], 'string', 'min' => 8, 'on' => self::SCENARIO_RENEW_PASSWORD],
        ];
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
        $payload = [
            "iss" => 'ch.tebe.notes',
            "iat" => time(),
            //"exp" => time() + (60*60*24),
            'user' => [
                'id' => $this->id,
                'name' => $this->username,
                'role' => $this->role,
                'scopes' => json_decode($this->scopes, true)
            ]
        ];
        $key = \Yii::$app->params['jwt.private_key'];
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    public function generateSalt()
    {
        return \Yii::$app->security->generateRandomString();
    }

    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'deleted' => 0
        ]);
    }

    public function validatePassword(string $password): bool
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

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->getScenario() === static::SCENARIO_CREATE) {
                if ($this->isNewRecord) {
                    $this->salt = $this->generateSalt();
                    $this->password = $this->hashPassword($this->password, $this->salt);
                    $this->created = new Expression('NOW()');
                } else {
                    $this->modified = new Expression('NOW()');
                }
            }
            if ($this->getScenario() === static::SCENARIO_RENEW_PASSWORD) {
                if (!$this->isNewRecord) {
                    $this->salt = $this->generateSalt();
                    $this->password = $this->hashPassword($this->password, $this->salt);
                    $this->modified = new Expression('NOW()');
                }
            }
            return true;
        }
        return false;
    }
}
