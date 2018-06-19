<?php

namespace app\controllers;

use Yii;
use app\models\CursoOfertado;
use app\models\CursoOfertadoSearch;
use app\models\Periodolectivo;
use app\models\Informacionpersonald;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use app\models\UploadForm;
use yii\web\UploadedFile;

/**
 * CursoofertadoController implements the CRUD actions for CursoOfertado model.
 */
class CursoofertadoController extends Controller
{
    /**
     * @inheritdoc
     */
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

    /**
     * Lists all CursoOfertado models.
     * @return mixed
     */
    public function actionIndex()
    {
		$periodo = Periodolectivo::find()
			->where(['StatusPerLec'=>1])
			->one();
		$usuario = Yii::$app->user->identity;
		#$hoy = date('Y-m-d');
		$this->view->params['docente'] = '';
		$this->view->params['cedula'] = '';
        //$searchModel = new DocenteperasigSearch();
		if ($usuario) {
			$this->view->params['docente'] = $usuario->ApellInfPer . ' ' . 
							$usuario->ApellMatInfPer . ' ' . $usuario->NombInfPer;
			$this->view->params['cedula'] = $usuario->CIInfPer;
			#$docente = Informacionpersonald::find()->where(['CIInfPer'=>$usuario->CIInfPer])->one();
			#if ($docente) {
			#		$this->view->params['docente'] = $docente->ApellInfPer . ' ' . 
			#				$docente->ApellMatInfPer . ' ' . $docente->NombInfPer;
			#		$this->view->params['cedula'] = $usuario->CIInfPer;
			#}
		}
        $searchModel = new CursoOfertadoSearch();
		$searchModel->idper = $periodo?$periodo->idper:'';
		#$searchModel->fecha_fin = $hoy;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		#echo var_dump($searchModel->docente); exit;
	

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CursoOfertado model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		/*
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
		*/
		return $this->redirect(['index']);
    }

    /**
     * Creates a new CursoOfertado model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
       /* $model = new CursoOfertado();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
		*/
		return $this->redirect(['index']);
    }

    /**
     * Updates an existing CursoOfertado model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		/*
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
		*/
		return $this->redirect(['index']);
    }

    /**
     * Deletes an existing CursoOfertado model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        # $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	public function actionHistorico()
    {
		$periodo = Periodolectivo::find()
			->where(['StatusPerLec'=>1])
			->one();
		$usuario = Yii::$app->user->identity;
		$this->view->params['docente'] = '';
		$this->view->params['cedula'] = '';
		
		if ($usuario && $periodo) {
			$this->view->params['docente'] = $usuario->ApellInfPer . ' ' . 
							$usuario->ApellMatInfPer . ' ' . $usuario->NombInfPer;
			$this->view->params['cedula'] = $usuario->CIInfPer;
			$query = CursoOfertado::find()
				->joinWith('detallemalla')
				->where(['iddocente' =>$usuario->CIInfPer])
				->andwhere(['!=','idper', $periodo->idper])
						->orderBy(['idper'=>SORT_DESC, 'detalle_malla.idmalla'=>SORT_ASC, 'detalle_malla.nivel'=>SORT_ASC, 
							'detalle_malla.idasignatura'=>SORT_ASC, 'paralelo'=>SORT_ASC]);
			$dataProvider = new ActiveDataProvider([
		        'query' => $query,
		    ]);
			$model = $dataProvider->getModels();
			return $this->render('historico', [
		        'model' => $model,
		        'dataProvider' => $dataProvider,
		    ]);
			
		}
        
		return $this->redirect(['index']);
    }


	public function actionUpload($idcurso)
	{
		$modelcurso = $this->findModel($idcurso);
		$model = new UploadForm();
		$this->view->params['docente'] = '';
		$this->view->params['cedula'] = '';
		$this->view->params['asignatura'] = '';
		if ($modelcurso) {
			$model->idcurso = $modelcurso->id;
			$this->view->params['docente'] = $modelcurso->getNombreDocente();
			$this->view->params['cedula'] = $modelcurso->iddocente;
			$this->view->params['asignatura'] = $modelcurso->detallemalla->asignatura->NombAsig;
			
			if (Yii::$app->request->isPost) {
				#echo var_dump('ok'); exit;
				try {
					$model->imageFile = UploadedFile::getInstance($model, 'imageFile');
					if ($model->upload()) {
						// file is uploaded successfully
						#echo var_dump($model->imageFile->name); exit;
						$modelcurso->silabo = $model->imageFile->name;
						$modelcurso->save();
						#return $this->redirect(['index']);
						Yii::$app->session->setFlash('success', "Your message to display");
					}
				}
				catch (\Exception $e) {
					#echo var_dump($e); exit;
				}
			}
		}
		else
			return $this->redirect(['index']);
		return $this->render('upload', ['model' => $model, 'modelcurso' => $modelcurso]);
	}

	public function actionPdf($idcurso) {
	    $model = $this->findModel($idcurso);

	    // This will need to be the path relative to the root of your app.
	    $filePath = 'uploads';
	    // Might need to change '@app' for another alias
	    $completePath = Yii::getAlias('@app'.$filePath.'/'.$model->silabo);

	    return Yii::$app->response->sendFile($completePath, $model->silabo, ['target'=>'_blank']);
	}

    /**
     * Finds the CursoOfertado model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CursoOfertado the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CursoOfertado::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
