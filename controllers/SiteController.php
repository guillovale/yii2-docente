<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\CambiarClave;
use app\models\ContactForm;
use app\models\Docenteperasig;
use app\models\User;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'cambiarclave'],
                'rules' => [
                    [
                        'actions' => ['logout', 'cambiarclave'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
			$usuario = Yii::$app->user->identity;
			$hoy = date('Y-m-d H:i:s');
			if ($usuario) {
				$email = $usuario->mailInst;
				$texto = 'Estimado docente: Usted ha ingresado correctamente al Sistema Académico UTELVT , 
						fecha: '.$hoy. 
						' en caso no haya sido usted, por favor comuníquese con nosotros en forma inmediata!';
				$this->enviarMail($email, $texto);
				
			}
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

	public function actionCambiarclave()
	{
		$identity = Yii::$app->user->identity;
	    $model = new CambiarClave;
 
		//$model = User::findByUsername($identity);
		$model->setScenario('changePwd');
 
    	if(isset($_POST['CambiarClave'])){
			$model->load(Yii::$app->request->post());
    		//$model->attributes = $_POST['CambiarClave'];
    		$valid = $model->validate();
 
			if($valid){
				//echo var_dump(Yii::$app->user); exit;
 				//$model = new User;
				//$modeluser = User::findByUsername($identity->LoginUsu);
				$modeluser = User::find()->where([
				    'LoginUsu'=>$identity->LoginUsu
				])->one();
				if (count($modeluser)) {
					$modeluser->ClaveUsu = md5($model->new_password);
				//$model->savePassword($model->new_password);
 
				//if($model->savePassword($model->new_password))
					if ($modeluser->save()) {
						//$this->redirect(array('cambiarclave','msg'=>'successfully changed password'));
						Yii::$app->session->setFlash('success', "password actualizado !");
						return $this->goHome();
					}
				}
				else
					$this->redirect(array('cambiarclave','msg'=>'password no cambiado'));
			}
		}
 		return $this->render('cambiarclave', [
            'model' => $model,
        ]); 

	}

	public function enviarMail($email, $texto)
	{
		$emailtics = 'tics@utelvt.edu.ec';
		$emailacademico = 'viceacademico@utelvt.edu.ec';
		if ($email === null || $email == '') {
				$email =$emailtics;
		}
		
		#echo var_dump($email); exit;
		try {
		
				
		$message = Yii::$app->mailer->compose();
		$message->setFrom(Yii::$app->params['adminEmail'])
				->setTo($email)
				//->setCc($emailtics)
				->setSubject('Ingreso al sistema')
				->setTextBody($texto)
				->send();

		$message = Yii::$app->mailer->compose();
		$message->setFrom(Yii::$app->params['adminEmail'])
				->setTo($emailtics)
				->setSubject('Ingreso al sistema')
				->setTextBody($texto)
				->send();
		}catch (Exception $e) {
					echo 'Excepción capturada: ',  $e->getMessage(), "\n";
				}
	}

}
