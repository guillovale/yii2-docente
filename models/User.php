<?php

namespace app\models;
//use app\models\Informacionpersonald as DbUser;

class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

	public static function tableName()
    {
        return 'informacionpersonal_d';
    }
	
	//public $id;
    //public $username;
    //public $password;
	
    public $authKey;
    public $accessToken;
	

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
		// return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
		
		return static::findOne($id);
		/*
		$dbUser = DbUser::find()
		        ->where([
		            "CIInfPer" => $id
		        ])
		        ->one();
		if (!count($dbUser)) {
			return null;
		}
		
		return new static($dbUser);
		*/
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
	/*
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
	*/
	
		$dbUser = static::find()
		        ->where(["LoginUsu" => $username, "StatusPer" => 1
		        ])
		        ->one();
		if (!count($dbUser)) {
			return null;
		}
		
			return new static($dbUser);
		//return $dbUser;
	}

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->CIInfPer;
    }

	/**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->LoginUsu;
    }


    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        //return $this->password === $password;
		//echo var_dump($password); exit;
		return $this->ClaveUsu === md5($password);
		//return $this->ClaveUsu === $password;
    }

	public function savePassword($password)
    {
		echo var_dump($this->ClaveUsu); exit;
		$this->ClaveUsu = md5($password);
        if (self::save())
            	return true;

		return false;
    }

}
