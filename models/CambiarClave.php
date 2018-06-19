<?php

namespace app\models;

use Yii;
use yii\base\Model;
//use app\models\Informacionpersonal_d;
/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class CambiarClave extends Model
{
    
    //Define public variable
 
    public $old_password;
    public $new_password;
    public $repeat_password;
 
 	private $_user = false;
    //Define the rules for old_password, new_password and repeat_password with changePwd Scenario.
 
    public function rules()
    {
		return [
            // username and password are both required
            [['old_password', 'new_password', 'repeat_password'], 'required', 'on' => 'changePwd'],
            // rememberMe must be a boolean value
            ['old_password', 'findPasswords', 'on' => 'changePwd'],
			['new_password', 'string', 'min' => 6],
            // password is validated by validatePassword()
            ['repeat_password', 'compare', 'compareAttribute'=>'new_password', 'on'=>'changePwd'],
        ];
      
    }

	 public function attributeLabels()
    {
        return [
            'old_password' => 'password actual',
            'new_password' => 'nuevo password',
			'repeat_password' => 'repetir password',
        ];
    }

    //matching the old password with your existing password.
    public function findPasswords($attribute, $params)
    {
		if (!$this->hasErrors()) {
			$user = $this->getUser();
			if (!$user)
				$this->addError($attribute, 'el usuario es incorrecto.');
			else
			//echo var_dump($user); exit;
        	//$user = User::model()->findByUsername(Yii::app()->user->id);
		    	if ($user->ClaveUsu != md5($this->old_password))
		        	$this->addError($attribute, 'Old password es incorrecto.');
		}
    }

	//matching the old password with your existing password.
    public function savePassword($password)
    {
		$user = $this->getUser();
		if ($user) {
			
			//echo var_dump($user); exit;
        	//$user = User::model()->findByUsername(Yii::app()->user->id);
           	return User::savePassword($password);
		}
		return false;
    }

	 /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
		$identity = Yii::$app->user->identity;
		
        if ($this->_user === false && $identity) {
		//$this->_user = User::findIdentity($this->username);
            $this->_user = User::findByUsername($identity->LoginUsu);
			//echo var_dump(get_class($this->_user)); exit;
        }

        return $this->_user;
    }

}
