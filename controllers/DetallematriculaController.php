<?php

namespace app\controllers;
use Yii;
use app\models\DetalleMatricula;
use app\models\Informacionpersonald;
use app\models\Periodolectivo;
use app\models\Carrera;
use app\models\Asignatura;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;

class DetallematriculaController extends \yii\web\Controller
{

	public function behaviors()
    {
        return [


		'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        //'actions' => ['delete','update', 'create'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($idcarr, $idasig, $nivel, $paralelo, $idper, $iddpa)
    {
	$periodo = Periodolectivo::find()
			->where(['StatusPerLec'=>1])
			->one();
	$carrera = Carrera::find()
			->where(['idCarr'=>$idcarr])
			->one();
	$asignatura = Asignatura::find()
			->where(['idAsig'=>$idasig])
			->one();
	$this->view->params['idper'] = $idper;
	$this->view->params['idcarr'] = $idcarr;
	$this->view->params['idasig'] = $idasig;
	$this->view->params['periodo'] = $periodo?$periodo->DescPerLec:'';
	$this->view->params['carrera'] = $carrera?$carrera->NombCarr:'';
	$this->view->params['asignatura'] = $asignatura?$asignatura->NombAsig:'';
	$this->view->params['nivel'] = $nivel;
	$this->view->params['paralelo'] = $paralelo;
	$identity = Yii::$app->user->identity;
        
	$docente = Informacionpersonald::find()->where(['CIInfPer'=>$identity->CIInfPer])->one();
	if ($docente) {
			$this->view->params['docente'] = $docente->ApellInfPer . ' ' . $docente->ApellMatInfPer . ' ' . $docente->NombInfPer;
			$this->view->params['cedula'] = $identity->CIInfPer;
	}
	
	$query = DetalleMatricula::find()
				->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
				->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
				->where(['idcarr'=>$idcarr, 'idasig' => $idasig, 'nivel' => $nivel, 
					'paralelo' => $paralelo, 'idper'=>$idper])
						->orderBy(['ApellInfPer'=>SORT_ASC]);
	
	$dataProvider = new ActiveDataProvider([
		'query' => $query,
		'pagination' => ['pagesize' => 80,],
	]);


	//$this->layout = "/cupos";
    	$searchModel = new DetalleMatricula();
	
	return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
	
    }

}
