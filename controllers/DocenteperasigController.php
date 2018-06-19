<?php

namespace app\controllers;

use Yii;
use app\models\Docenteperasig;
use app\models\DocenteperasigSearch;
use app\models\Periodolectivo;
use app\models\Informacionpersonald;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
/**
 * DocenteperasigController implements the CRUD actions for Docenteperasig model.
 */
class DocenteperasigController extends Controller
{
    /**
     * @inheritdoc
     */
	public function behaviors()
    {
        return [


		'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete','update', 'create', 'index'],
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
    /**
     * Lists all Docenteperasig models.
     * @return mixed
     */
    public function actionIndex()
    {
	$periodo = Periodolectivo::find()
			->where(['StatusPerLec'=>1])
			->one();
	$this->view->params['periodo'] = $periodo?$periodo->DescPerLec:'';
	// the current user identity. `null` if the user is not authenticated.
	$identity = Yii::$app->user->identity;
        //$searchModel = new DocenteperasigSearch();
	$docente = Informacionpersonald::find()->where(['CIInfPer'=>$identity->CIInfPer])->one();
	if ($docente) {
			$this->view->params['docente'] = $docente->ApellInfPer . ' ' . $docente->ApellMatInfPer . ' ' . $docente->NombInfPer;
			$this->view->params['cedula'] = $identity->CIInfPer;
	}
	//$searchModel->idPer = $periodo?$periodo->idper:'';
	//$searchModel->CIInfPer = $cedula;
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

	$query = Docenteperasig::find()
				->where(['CIInfPer' =>$identity->CIInfPer, 'idper' => 108])
						->orderBy(['idasig'=>SORT_ASC]);
	
	//					->andWhere(['!=','status',2]);
	//        $query = DetalleMatricula::find();
		
	$dataProvider = new ActiveDataProvider([
		'query' => $query,
	]);


	//$this->layout = "/cupos";
    	$searchModel = new Docenteperasig();
	//$searchModel->idPer = $periodo;
	//echo var_dump($idfactura); exit;
       // $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

	return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Docenteperasig model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Docenteperasig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
	/*
        $model = new Docenteperasig();
	
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->dpa_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
	*/
    }

    /**
     * Updates an existing Docenteperasig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
	/*
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->dpa_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
	*/
    }

    /**
     * Deletes an existing Docenteperasig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
	/*
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
	*/
    }

    /**
     * Finds the Docenteperasig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Docenteperasig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Docenteperasig::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
