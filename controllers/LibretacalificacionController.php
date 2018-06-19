<?php

namespace app\controllers;

use Yii;
use app\models\LibretaCalificacion;
use app\models\LibretaCalificacionSearch;
use app\models\Periodolectivo;
use app\models\Carrera;
use app\models\CursoOfertado;
use app\models\Asignatura;
use app\models\NotasDetalle;
use app\models\Notasalumnoasignatura;
use app\models\Componentescalificacion;
use app\models\Parametroscalificacion;
use app\models\DetalleMatricula;
use app\models\Docenteperasig;
use app\models\ExtensionDocente;
use app\models\DetalleHorario;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\data\SqlDataProvider;
use yii\base\Model;
require_once(__DIR__ . '/../vendor/tcpdf/tcpdf.php');
//usar clase TCPDF
use TCPDF;
/**
 * LibretaCalificacionController implements the CRUD actions for LibretaCalificacion model.
 */
class LibretacalificacionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [


		'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete','update', 'create', 'index', 'consolidado', 'imprimir'],
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
     * Lists all LibretaCalificacion models.
     * @return mixed
     */
    public function actionIndex($idcurso)
    {
		#$periodo = Periodolectivo::find()
		#	->where(['StatusPerLec'=>1])
		#	->one();
		$usuario = Yii::$app->user->identity;
		
		if ($usuario) {
			$modelcurso = $this->findCurso($idcurso);
			if ($modelcurso) {
				$hoy  = date('Y-m-d');
				$extdmodel = ExtensionDocente::find()
					->where(['idcurso' => $modelcurso->id])
					->andwhere(['<=','fecha_inicio', $hoy])
					->andwhere(['>=','fecha_fin', $hoy])
					->one();
				#echo var_dump($hoy, $extdmodel, $modelcurso); exit;
				if ($modelcurso->fecha_fin >= $hoy || $extdmodel) {
					
					$cedula = $usuario->CIInfPer;
					$this->layout = "/lista_alumnos";
					$searchModel = new LibretaCalificacionSearch();
					$searchModel->iddocente = $cedula;
					$searchModel->idcurso = $idcurso;
					$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
					$models = $dataProvider->getModels();
					$idper = $modelcurso->idper;
					$totalcomponentes = ['A','B','C','X','T'];
					
					$componentes = [];
					foreach ($models as $model) {
						if ($model->hemisemestre == 1) {
							if (!in_array($model->getParametrosigla(), $componentes))
								array_push($componentes, $model->getParametrosigla());
						}
					}
					
					$numcomponentes = count(array_diff($totalcomponentes, $componentes));
					
					Url::remember();
					
					
					$carrera = Carrera::find()
							->where(['idCarr'=>$modelcurso->detallemalla->malla->idcarrera])
							->one();
					$asignatura = Asignatura::find()
						->where(['idAsig'=>$modelcurso->detallemalla->idasignatura])
						->one();

					$totalalumnos = DetalleMatricula::find()
									->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
									->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
									->where(['idcurso'=>$idcurso, 'estado'=>1])
									->orderBy(['ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC]);

					$horarios = Detallehorario::find()
									->where(['idcurso'=>$idcurso]);
									
					$this->view->params['idper'] = $idper;
					$this->view->params['idcurso'] = $idcurso;
					$this->view->params['idcarr'] = $modelcurso->detallemalla->malla->idcarrera;
					$this->view->params['idasig'] = $modelcurso->detallemalla->idasignatura;
					$this->view->params['periodo'] = $modelcurso->periodo->DescPerLec;
					$this->view->params['carrera'] = $carrera?$carrera->NombCarr:'';
					$this->view->params['asignatura'] = $asignatura?$asignatura->NombAsig:'';
					$this->view->params['nivel'] = $modelcurso->detallemalla->nivel;
					$this->view->params['paralelo'] = $modelcurso->paralelo;
					$this->view->params['silabo'] = $modelcurso->silabo;
					$this->view->params['numcomponentes'] = $numcomponentes;
					$this->view->params['totalalumnos'] = $totalalumnos;
					$this->view->params['horarios'] = $horarios;
					#echo var_dump($searchModel->all()); exit;
					return $this->render('index', [
						'searchModel' => $searchModel,
						'dataProvider' => $dataProvider,
					]);
				}
			}
		}
		return $this->redirect(Yii::$app->request->referrer);

    }

    /**
     * Displays a single LibretaCalificacion model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		$usuario = Yii::$app->user->identity;
		$sigla = ['A','B','C','X','T'];
		$periodo = $this->getPeriodo();
		$hoy  = date('Y-m-d');
		$grabar = 0;
		$habilitar = true;
		$model = $this->findModel($id);
		
		
		if ( $model && $usuario) {
			$tipo_ingreso = isset($model->curso->detallemalla->malla->detalle)?
					$model->curso->detallemalla->malla->detalle:'';
			$optativa = $model->curso->detallemalla->malla->carrera->optativa;
			$this->view->params['idasig'] = $model?$model->getAsignatura():'';

			$extdmodel = ExtensionDocente::find()
				->where(['idcurso' => $model->curso->id])
				->andwhere(['<=','fecha_inicio', $hoy])
				->andwhere(['>=','fecha_fin', $hoy])
				->one();
			
			if ( $periodo && ($model->curso->fecha_fin >= $hoy) ) {

				if ( ('SNNA' == substr($tipo_ingreso, 0, 4)) && ($hoy >= $periodo->finiciohemi1) ) {
					$sigla = ['N','M'];
					#$hemisemestre = [0=>0];
					$grabar = 1;
					$habilitar = false;
					#$hemi = 0;
				}
				elseif ($model->hemisemestre == 1 && ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1) && 
						$optativa != 1) {
					$grabar = 1;
					$habilitar = false;
					#$hemisemestre = [1=>1];
					#$hemi = 1;
				}
				elseif ($model->hemisemestre == 2 && ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2) && 
						$optativa != 1) {
					$grabar = 1;
					$habilitar = false;
					#$hemisemestre = [2=>2];
					#$hemi = 2;
				}
				elseif ($model->hemisemestre == 0 && $hoy >= $periodo->examsupletorio_ini && 
						$hoy <= $periodo->examsupletorio_fin && 
						$optativa != 1) {
					$grabar = 1;	
					$habilitar = false;			
					#$hemi = 0;
					$sigla = ['R'];	
					#$hemisemestre = [0=>0];
				}
				elseif ( $optativa == 1 ) {
					$grabar = 0;
					#$habilitar = false;
					#$hemisemestre = [1=>1, 2=>2];
					$sigla = ['A','B','C','X','T'];
					if ($model->hemisemestre == 0){
						$sigla = ['R'];
						#$habilitar = false;
						#$hemisemestre = [0=>0];
					}
				}
			}
			if ( $extdmodel) {
				$habilitar = false;
				$grabar = 1;
				if ('SNNA' == substr($tipo_ingreso, 0, 4)) {
					$sigla = ['N','M'];
					$hemisemestre = [0=>0];
				}
				else {	
					$hemisemestre = [1=>1, 2=>2, 0=>0];
					$sigla = ['A','B','C','X','T', 'R'];
				}
			}
		
			$componente = ArrayHelper::map(componentescalificacion::find()
							->joinWith('idParam')
							#->select('idcomponente, componente')
							->where(['in', 'parametroscalificacion.sigla', $sigla])
							->asArray()
							->all(), 'idcomponente', 'componente');
			$parametro = ArrayHelper::map(parametroscalificacion::find()
							->select('idparametro, parametro')
							->where(['in', 'sigla', $sigla])
							->asArray()
							->all(), 'idparametro', 'parametro');
			//echo var_dump($model->hemisemestre, ' ',$parametro, ' ' , $sigla, ' ', $periodo->ffinhemi2); exit;
			$this->view->params['componente'] = $componente;
			$this->view->params['parametro'] = $parametro;
			$this->view->params['habilitar'] = $habilitar;
			$idlibreta = $model->id;
			//$libretaModel['iddocenteperasig'] = $iddpa;
			
			$modelNotas = NotasDetalle::find()
							->leftJoin('detalle_matricula', 'detalle_matricula.id = notas_detalle.iddetallematricula')
							->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
							->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
							->where(['idlibreta'=> $model->id])
							->orderBy(['ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC]);
							//->all();
			$dataProvider = new ActiveDataProvider([
				'query' => $modelNotas,
				'pagination' => [
				'pageSize' => 80,
				],
			]);
			
			//////////////////////////////////////////////////////////

			
			//////////////////////////////////

			
			if ($model->load(Yii::$app->request->post())) {
				#&& $publicar == 0
				if ($grabar == 1 && $usuario) {
					$model->save();
				}
				return $this->redirect(Url::previous());
			}
			//echo var_dump(Yii::$app->request->post(), ' ', $id, $model->getErrors()); exit;
		    return $this->render('view', [
		        'model' => $model, 'dataProvider' => $dataProvider, 'idlibreta' => $idlibreta
		    ]);
		}
		return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Creates a new LibretaCalificacion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$publicado = Docenteperasig::find()
									->where(['dpa_id'=> $iddpa])->one();
		$publicar = $publicado?$publicado->publicar:0;
		$periodo = $this->getPeriodo();
		$hoy  = date('Y-m-d');
		$crear = 0;
		$model = new LibretaCalificacion();
		if ($periodo) {
			 

			//echo var_dump($periodo->finiciohemi1,'_', $hoy, '-', $periodo->ffinhemi1); exit;
			if ($model->hemisemestre == 1 && ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1)) {
				$crear = 1;
			}
			if ($model->hemisemestre == 2 && ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2)) {
				$crear = 1;
			}
			if ($model->hemisemestre == 0 && ($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin)) {
				$crear = 1;
			}
		}
		//////////////////////////////////

        if ($model->load(Yii::$app->request->post()) && $crear == 1 && $publicar == 0) {
			$model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

	public function actionConsolidado($idcurso)
    {
		$modelcurso = $this->findCurso($idcurso);
		if ($modelcurso) {

		$sql = "SELECT c.cedula, c.nombre, c.idnota, c.iddetallematricula, 
			max(c.GA1) as GA1, max(c.NGA1) as NGA1, max(c.GA2) as GA2 , 
			max(c.NGA2) as NGA2, max(c.PA) as PA , max(c.NPA) as NPA , max(c.X1) as X1, max(c.NX1) as NX1,
			max(c.X2) as X2, max(c.NX2) as NX2,
			round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5) as suma, 
			max(c.M) as MJ, max(c.NM) as NM,
			max(c.AT) as AT, max(c.NAT) as NAT,
			if( max(c.NAT) >= 7 and (max(c.NM) >= 7 or 
					round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5) >= 7),
			 'Aprobada','Reprobada') as Estado,
			if(max(c.NM) > round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5), max(c.NM),
				round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5)	 ) as notafinal
			from(
			SELECT notas_detalle.idnota, libreta_calificacion.idcurso, libreta_calificacion.id, libreta_calificacion.idcomponente,
			notas_detalle.iddetallematricula, notas_detalle.nota, 
			if(libreta_calificacion.idcomponente = 27, notas_detalle.idnota, '') as GA1,
			if(libreta_calificacion.idcomponente = 27, notas_detalle.nota, '') as NGA1,
			if(libreta_calificacion.idcomponente = 28, notas_detalle.idnota, '') as GA2,
			if(libreta_calificacion.idcomponente = 28, notas_detalle.nota, '') as NGA2,
			if(libreta_calificacion.idcomponente = 29, notas_detalle.idnota, '') as PA,
			if(libreta_calificacion.idcomponente = 29, notas_detalle.nota, '') as NPA,
			if(libreta_calificacion.idcomponente = 30, notas_detalle.idnota, '') as X1,
			if(libreta_calificacion.idcomponente = 30, notas_detalle.nota, '') as NX1,
			if(libreta_calificacion.idcomponente = 31, notas_detalle.idnota, '') as X2,
			if(libreta_calificacion.idcomponente = 31, notas_detalle.nota, '') as NX2,
			if(libreta_calificacion.idcomponente = 32, notas_detalle.idnota, '') as M,
			if(libreta_calificacion.idcomponente = 32, notas_detalle.nota, '') as NM, 
			if(libreta_calificacion.idcomponente = 33, notas_detalle.idnota, '') as AT,
			if(libreta_calificacion.idcomponente = 33, notas_detalle.nota, '') as NAT,
			concat(informacionpersonal.ApellInfPer, ' ', informacionpersonal.ApellMatInfPer, ' ',
				informacionpersonal.NombInfPer ) as nombre, factura.cedula
			FROM `notas_detalle`
			LEFT JOIN libreta_calificacion on libreta_calificacion.id = notas_detalle.idlibreta
			LEFT JOIN curso_ofertado on curso_ofertado.id = libreta_calificacion.idcurso
			LEFT JOIN detalle_matricula on detalle_matricula.id = notas_detalle.iddetallematricula
			LEFT JOIN factura on factura.id = detalle_matricula.idfactura
			LEFT JOIN informacionpersonal on informacionpersonal.CIInfPer = factura.cedula
			where curso_ofertado.id = $idcurso) c
			GROUP by c.iddetallematricula 
			order by c.nombre";
			#echo var_dump($modelcurso); exit;
			$this->view->params['idper'] = $modelcurso->idper;
			$this->view->params['idcurso'] = $idcurso;
			$this->view->params['idcarr'] = $modelcurso->detallemalla->malla->idcarrera;
			$this->view->params['idasig'] = $modelcurso->detallemalla->idasignatura;
			$this->view->params['periodo'] = $modelcurso->periodo->DescPerLec;
			$this->view->params['carrera'] = $modelcurso->detallemalla->malla->carrera->NombCarr;
			$this->view->params['asignatura'] = $modelcurso->detallemalla->asignatura->NombAsig;
			$this->view->params['nivel'] = $modelcurso->detallemalla->nivel;
			$this->view->params['paralelo'] = $modelcurso->paralelo;
				#echo var_dump($sql); exit;
			#$query = new Query;
		    #$query1 = Yii::$app->db->createCommand($sql);
			#$query->select($sql);
			#$model = $query1->queryAll();
			#echo var_dump($model);exit;
		//////////////////////////////////
			
			$provider = new SqlDataProvider([
				'sql' => $sql,
				#'params' => [':status' => 1],
				#'totalCount' => $count,
				'pagination' => [
					'pageSize' => 100,
				],
				#'sort' => [
				#	'attributes' => [
				#		'title',
				#		'view_count',
				#		'created_at',
				#	],
				#],
			]);
        
			$model = $provider->getModels();
			#foreach($model as $dato) {
			#	echo var_dump($dato['suma']);			
			#}#exit;
			 #$sourceModel = new \namespace\YourGridModel;
   			 #$dataProvider = $sourceModel->search(Yii::$app->request->getQueryParams());
   			 #$models = $dataProvider->getModels();
			#if (Model::loadMultiple($model, Yii::$app->request->post())) {
				#echo var_dump($model[0]['suma'], Yii::$app->request->getQueryParams());exit;
			#}

            return $this->render('consolidado', [
				'dataProvider' => $provider,
                'model' => $model,
				'idcurso'=> $idcurso,
            ]);
		}
		return $this->redirect(Url::previous());
       
    }
    /**
     * Updates an existing LibretaCalificacion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		#$publicado = Docenteperasig::find()
		#							->where(['dpa_id'=> $iddpa])->one();
		#$publicar = $publicado?$publicado->publicar:0;
		$usuario = Yii::$app->user->identity;
		$periodo = $this->getPeriodo();
		$hoy  = date('Y-m-d');
		if ($periodo && $usuario) {
			$model = $this->findModel($id);

			//echo var_dump($periodo->finiciohemi1,'_', $hoy, '-', $periodo->ffinhemi1); exit;
			if ($model->hemisemestre == 1 && ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1)) {
				if ($model->load(Yii::$app->request->get()) )
					$model->save();
			}
			if ($model->hemisemestre == 2 && ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2)) {
				if ($model->load(Yii::$app->request->get()) )
					$model->save();
			}
			if ($model->hemisemestre == 0 && ($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin)) {
				if ($model->load(Yii::$app->request->get()) )
					$model->save();
			}
		    //if ($model->load(Yii::$app->request->get()) && $model->save()) {
		    //    return $this->redirect(['view', 'id' => $model->id]);
		    //} else {
		    //    return $this->render('update', [
		    //        'model' => $model,
		    //    ]);
			
        }
		//echo var_dump(Yii::$app->request->post(), ' ', $id); exit;
		return $this->redirect(Url::previous());
    }

    /**
     * Deletes an existing LibretaCalificacion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {	
		$usuario = Yii::$app->user->identity;
		$periodo = $this->getPeriodo();
		$model = $this->findModel($id);
		$hoy  = date('Y-m-d');
		$extdmodel = NULL;
				
		if ($model && $usuario ) {
			$carrera = $model->curso->detallemalla->malla->carrera->idCarr?
					$model->curso->detallemalla->malla->carrera->idCarr:'';
			$optativa = $model->curso->detallemalla->malla->carrera->optativa;
			$tipo_ingreso = isset($model->curso->detallemalla->malla->detalle)?
					$model->curso->detallemalla->malla->detalle:'';

			$extdmodel = ExtensionDocente::find()
				->where(['idcurso' => $model->curso->id])
				->andwhere(['<=','fecha_inicio', $hoy])
				->andwhere(['>=','fecha_fin', $hoy])
				->one();
			
			if ( $periodo && ($model->curso->fecha_fin >= $hoy) ) {
							
				if ( ('SNNA' == substr($tipo_ingreso, 0, 4)) && ($hoy >= $periodo->finiciohemi1) ) {
					$model->delete();
				}

				elseif ( $model->hemisemestre == 1 && 
					($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1)) {
					$model->delete();
				}
				elseif ( $model->hemisemestre == 2 && 
					($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2)) {
					$model->delete();
				}
				elseif ( $model->hemisemestre == 0 && ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2)) {
					#($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin)) {
					$model->delete();
					//echo var_dump($periodo->examsupletorio_ini,'_', $publicar, '-', $periodo->examsupletorio_fin); exit;
				}
				elseif ( $optativa == 1 ) {
					#$model->delete();
					//echo var_dump($periodo->examsupletorio_ini,'_', $publicar, '-', $periodo->examsupletorio_fin); exit;
				}
			}
		}
		
		if ( $extdmodel ) {
			$model->delete();
		}

        return $this->redirect(Yii::$app->request->referrer);
    }

	public function actionListar($id)
    {
		//echo var_dump($id); exit;
        $countPosts = Parametroscalificacion::find()
                ->where(['idparametro' => $id])
                ->count();
 
        $posts = Componentescalificacion::find()
                ->where(['idparametro' => $id])
                ->orderBy('componente ASC')
                ->all();
 
        if($countPosts>0){
            foreach($posts as $post){
                echo "<option value='".$post->idcomponente."'>".$post->componente."</option>";
            }
        }
        else{
            echo "<option>-</option>";
        }
 
    }

	public function actionListarcomponente($id)
    {
		//echo var_dump($id); exit;
		if ($id == 0) {
			$sigla = ['R'];
		}
		else {
			$sigla = ['A','B','C','X','T'];
		}
		
        $posts = Parametroscalificacion::find()
                #->where(['idparametro' => $id])
				->Where(['in', 'sigla', $sigla])
				->orderBy('sigla ASC')
                ->all();
                #->count();
 
        $postsc = Componentescalificacion::find()
				->joinWith('idParam')
                ->where(['parametroscalificacion.sigla' => $sigla])
                ->orderBy('componente ASC')
                ->all();
 
        if($posts){
			echo "<option>-</option>";
            foreach($posts as $post){
                echo "<option value='".$post->idparametro."'>".$post->parametro."</option>";
            }
        }
        else{
            echo "<option>-</option>";
        }
 
    }

	public function actionAgregar1($idlibreta)
    {	
		$periodo = $this->getPeriodo();
		$model = $this->findModel($idlibreta);
		$hoy  = date('Y-m-d');
		if ($periodo && $model) {
			
			if ( ( $model->hemisemestre == 1 && ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1)) or 
					($model->hemisemestre == 2 && ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2))
				or ($model->curso->detallemalla->malla->carrera->optativa == 1)
			){
				$idper = $periodo->idper;

				$modelNotas = NotasDetalle::find()
							->select('iddetallematricula')
							->where(['idlibreta'=> $idlibreta])->column();
				$detallematricula = DetalleMatricula::find()
							->select('detalle_matricula.id')
							->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
							->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
							->where(['idcurso'=>$model->idcurso, 'estado' => 1])
							->andWhere(['not in','detalle_matricula.id', $modelNotas])
							->all();

				//echo var_dump($detallematricula); exit;
				if ($detallematricula) {
					foreach($detallematricula as $detalle){
						//echo var_dump($detalle->id); exit;
						$modelnota = new NotasDetalle();
						$modelnota->idlibreta = $idlibreta;
						$modelnota->iddetallematricula = $detalle->id;
						$modelnota->nota = 0;
						$modelnota['fecha_crea'] = date("Y-m-d H:i:s");
						$modelnota->save();
					}
				}
			}
			
		}

        return $this->redirect(Yii::$app->request->referrer);
    }

	public function actionAgregar($idcurso)
    {	
		$modelLibreta = LibretaCalificacion::find()->where(['idcurso'=> $idcurso])->all();
		$hoy  = date('Y-m-d');
		if ($modelLibreta) {
			foreach($modelLibreta as $libreta) {
				$modelNotas = NotasDetalle::find()
							->select('iddetallematricula')
							//->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
							//->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
							->where(['idlibreta'=> $libreta->id])->column();
				$detallematricula = DetalleMatricula::find()
							->select('detalle_matricula.id')
							->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
							->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
							->where(['idcurso'=>$idcurso, 'estado' => 1])
							#->where(['idcarr'=>$idcarr, 'idasig' => $idasig, 'nivel' => $nivel, 
							#	'paralelo' => $paralelo, 'idper'=>$idper])
							->andWhere(['not in','detalle_matricula.id', $modelNotas])
							//->orderBy(['ApellInfPer'=>SORT_ASC])
							->all();
							//->column();

				
				if ($detallematricula) {
					foreach($detallematricula as $detalle){
						$modelnota = new NotasDetalle();
						$modelnota->idlibreta = $libreta->id;
						$modelnota->iddetallematricula = $detalle->id;
						$modelnota->nota = 0;
						$modelnota['fecha_crea'] = date("Y-m-d H:i:s");
						$modelnota->save();
					}
				}
			}
			
		}
		return $this->redirect(Yii::$app->request->referrer);
	}

	public function actionImprimir($idcurso)
    {

		$cursomodel = $this->findCurso($idcurso);
		$idcursomodel = $cursomodel?$cursomodel->id:0;
		$idper = $cursomodel?$cursomodel->idper:'';
		$periodo = $cursomodel?$cursomodel->periodo->DescPerLec:'';
		$asignatura = $cursomodel?$cursomodel->detallemalla->asignatura->NombAsig:'';
		$idasignatura = $cursomodel?$cursomodel->detallemalla->idasignatura:'';
		$docente = $cursomodel?$cursomodel->getNombreDocente():'';
		$iddocente = $cursomodel?$cursomodel->iddocente:'';
		$carrera = $cursomodel?$cursomodel->detallemalla->malla->carrera->NombCarr:'';
		$nivel = $cursomodel?$cursomodel->detallemalla->nivel:'';
		$paralelo = $cursomodel?$cursomodel->paralelo:'';

		$sql = "SELECT c.cedula, c.nombre, c.idnota, c.iddetallematricula, c.idper, c.idasignatura,
			max(c.GA1) as GA1, max(c.NGA1) as NGA1, max(c.GA2) as GA2 , 
			max(c.NGA2) as NGA2, max(c.PA) as PA , max(c.NPA) as NPA , max(c.X1) as X1, max(c.NX1) as NX1,
			max(c.X2) as X2, max(c.NX2) as NX2,
			round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5) as suma, 
			max(c.M) as MJ, max(c.NM) as NM,
			max(c.AT) as AT, max(c.NAT) as NAT,
			if( max(c.NAT) >= 7 and (max(c.NM) >= 7 or 
					round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5) >= 7),
			 'APROBADA','REPROBADA') as Estado,
			if( max(c.NAT) >= 7 and (max(c.NM) >= 7 or 
					round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5) >= 7),
			 1,0) as aprobada, 
			if(max(c.NM) > round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5), max(c.NM),
				round((max(c.NGA1) + max(c.NGA2) + max(c.NPA) + max(c.NX1) + max(c.NX2))/5)	 ) as notafinal
			from(
			SELECT notas_detalle.idnota, libreta_calificacion.idcurso, libreta_calificacion.id, libreta_calificacion.idcomponente,
			notas_detalle.iddetallematricula, notas_detalle.nota, curso_ofertado.idper, detalle_malla.idasignatura,
			if(libreta_calificacion.idcomponente = 27, notas_detalle.idnota, '') as GA1,
			if(libreta_calificacion.idcomponente = 27, notas_detalle.nota, '') as NGA1,
			if(libreta_calificacion.idcomponente = 28, notas_detalle.idnota, '') as GA2,
			if(libreta_calificacion.idcomponente = 28, notas_detalle.nota, '') as NGA2,
			if(libreta_calificacion.idcomponente = 29, notas_detalle.idnota, '') as PA,
			if(libreta_calificacion.idcomponente = 29, notas_detalle.nota, '') as NPA,
			if(libreta_calificacion.idcomponente = 30, notas_detalle.idnota, '') as X1,
			if(libreta_calificacion.idcomponente = 30, notas_detalle.nota, '') as NX1,
			if(libreta_calificacion.idcomponente = 31, notas_detalle.idnota, '') as X2,
			if(libreta_calificacion.idcomponente = 31, notas_detalle.nota, '') as NX2,
			if(libreta_calificacion.idcomponente = 32, notas_detalle.idnota, '') as M,
			if(libreta_calificacion.idcomponente = 32, notas_detalle.nota, '') as NM, 
			if(libreta_calificacion.idcomponente = 33, notas_detalle.idnota, '') as AT,
			if(libreta_calificacion.idcomponente = 33, notas_detalle.nota, '') as NAT,
			concat(informacionpersonal.ApellInfPer, ' ', informacionpersonal.ApellMatInfPer, ' ',
				informacionpersonal.NombInfPer ) as nombre, factura.cedula
			FROM `notas_detalle`
			LEFT JOIN libreta_calificacion on libreta_calificacion.id = notas_detalle.idlibreta
			LEFT JOIN curso_ofertado on curso_ofertado.id = libreta_calificacion.idcurso
			LEFT JOIN detalle_malla on detalle_malla.id = curso_ofertado.iddetallemalla
			LEFT JOIN detalle_matricula on detalle_matricula.id = notas_detalle.iddetallematricula
			LEFT JOIN factura on factura.id = detalle_matricula.idfactura
			LEFT JOIN informacionpersonal on informacionpersonal.CIInfPer = factura.cedula
			where curso_ofertado.id = $idcurso) c
			GROUP by c.iddetallematricula 
			order by c.nombre";

		$provider = new SqlDataProvider([
				'sql' => $sql,
				#'params' => [':status' => 1],
				#'totalCount' => $count,
				'pagination' => [
					'pageSize' => 100,
				],
				#'sort' => [
				#	'attributes' => [
				#		'title',
				#		'view_count',
				#		'created_at',
				#	],
				#],
			]);
        
		$rows = $provider->getModels();
				#echo var_dump($sql); exit;
			#$query = new Query;
		    #$query1 = Yii::$app->db->createCommand($sql);
			#$query->select($sql);
			#$model = $query1->queryAll();
			#echo var_dump($model);exit;
		//////////////////////////////////
		$pdf = new MYPDF();	 
		//echo var_dump($rows, ' ', $ids); exit;
		$img_file = K_PATH_IMAGES.'logo.jpg';

		// set document information
		$pdf->SetCreator(PDF_CREATOR);  
		$pdf->SetAuthor('tics');
		$pdf->SetTitle("Notas publicadas");                
		//$pdf->SetHeaderData(PDF_HEADER_LOGO,PDF_HEADER_LOGO_WIDTH, "UNIVERSIDAD TÉCNICA LUIS VARGAS TORRES DE ESMERALDAS"., "" .
		//		 "\n" . "Esmeraldas-Ecuador");
		$pdf->setFooterData(array(0,64,0), array(0,64,128));

		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->SetFont('helvetica', '', 8);
		$pdf->SetTextColor(0,0,0);
	
			// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();

		$fecha = date('d-m-Y');
		//$row = $query->one();
		//$asignatura = $row["NombAsig"];
		//$periodo = $row["DescPerLec"];
		//$carrera = $row["NombCarr"];
		//$periodo = $row["DescPerLec"];
		$html = "<div style='margin-bottom:12px;'>
		Esmeraldas, $fecha 	.	.	.	.	.	Período: $periodo
		 <br><address>
			<b>Asignatura: $asignatura  </b><br>
			Docente: $docente <br>
			Carrera: $carrera .	.	.
			Nivel: $nivel.	.	.
			Paralelo: $paralelo <br>
			</address>  
			</div>";
		//Convert the Html to a pdf document
		$pdf->writeHTML($html, true, false, true, false, '');
	 
		$header = array('Cédula', 'Alumno', 'Nota Final', 'Asistencia', 'Observación'); 
	 
		// print colored table
		$this->ColoredTable($pdf,$header, $rows);
		$pdf->setY(263);
		//if(isset($matricula)){
		$pdf->write1DBarcode($idcursomodel, 'C39', '', '', '', 5, 0.2, '', 'N');
		$pdf->Cell(0, 0, $idcursomodel, 0, 1);
		

		// reset pointer to the last page
		$pdf->lastPage();
		$file = $iddocente . '_' . $idcursomodel . '.' . 'pdf';
		//Close and output PDF document
		$pdf->Output($file, 'D');	
       
       
    }

	// Colored table
    public function ColoredTable($pdf,$header,$data) {
        // Colors, line width and bold font
        $pdf->SetFillColor(120, 185, 120);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(120, 185, 120);
        $pdf->SetLineWidth(0.3);
        $pdf->SetFont('', 'B', '7');
        // Header
        $w = array(20, 70, 15, 20, 50);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $pdf->Cell($w[$i], 5, $header[$i], 1, 0, 'C', 1);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        // Data
        $fill = 0;
	
        foreach($data as $row) {
		
			


			#echo var_dump($row['suma']);
			#exit;
			$observacion = $row['Estado'];
			$pdf->Cell($w[0], 6, $row['cedula'], 'LR', 0, 'L', $fill);
			#$pdf->Cell($w[1], 6, number_format($row['idsemestre']), 'LR', 0, 'C', $fill);
			$pdf->Cell($w[1], 6, $row['nombre'], 'LR', 0, 'L', $fill);
			$pdf->Cell($w[2], 6, $row['notafinal'], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[3], 6, ($row['NAT']*10).'%', 'LR', 0, 'C', $fill);
			#$pdf->Cell($w[3], 6, $row['observacion'], 'LR', 0, 'C', $fill);
			$pdf->Cell($w[4], 6, $observacion, 'LR', 0, 'L', $fill);
			$pdf->Ln();
			$fill=!$fill;
			
			//***************************************************************************
			$notasalumno = Notasalumnoasignatura::find()
										->where(['iddetalle'=> $row["iddetallematricula"]])
										->one();
				#$alumno = $matricula?$matricula->idFactura0->cedula:'';
				
				if ($notasalumno) {
					#$alumno = $notasalumno?$notasalumno->CIInfPer:'';
					#echo var_dump($matricula->id); exit;
					#$notasalumno->idPer = $idper;
					#$notasalumno->CIInfPer = $alumno;					
					$notasalumno->CalifFinal = $row["notafinal"];
					$notasalumno->asistencia = $row["NAT"]*10;
					$notasalumno->StatusCalif = 3;
					$notasalumno->observacion = $row["Estado"];
					$notasalumno->aprobada = $row["aprobada"];
					$notasalumno->save();
					#if ($notasalumno->iddetalle == 74252) {
					#	echo var_dump($notasalumno->getErrors(), '-', $nota, '-', $notasalumno); exit;}
					#echo var_dump($notasalumno->getErrors(), $notasalumno->CalifFinal); exit;
				}
				else {
					#echo var_dump($matricula->id); exit;
					#$alumno = $matricula?$matricula->idFactura0->cedula:'';
					$modelnota = new Notasalumnoasignatura();
					$modelnota->idPer = $row["idper"];
					$modelnota->CIInfPer = $row["cedula"];
					$modelnota->idAsig = $row["idasignatura"];
					$modelnota->iddetalle = $row["iddetallematricula"];
					$modelnota->CalifFinal = $row["notafinal"];
					$modelnota->asistencia = $row["NAT"]*10;
					$modelnota->StatusCalif = 3;
					$modelnota->observacion = $row["Estado"];
					$modelnota->aprobada = $row["aprobada"];
					$modelnota->VRepite = 1;
					$modelnota->registro = date('Y-m-d H:i:s');
					$modelnota->convalidacion = 0;
						//$modelnota->observacion_efa = 'nota homologada';
					$modelnota->save();
					#echo var_dump($modelnota); exit;
					#echo var_dump($modelnota->errors); exit;
					
				}
				
			


			#**************************************************************************


        }
	
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }


    /**
     * Finds the LibretaCalificacion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LibretaCalificacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */

	protected function getPeriodo()
	{
				
		$periodo = Periodolectivo::find()
			->where(['StatusPerLec'=> 1])
				->one();
		return $periodo?$periodo:null;
	}

    protected function findModel($id)
    {
        if (($model = LibretaCalificacion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	protected function findCurso($idcurso)
    {
        if (($model = CursoOfertado::findOne($idcurso)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
}

//**********************************************************************************************************************************
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
		
	// Set font
	$this->SetFont('helvetica', 'B', 11);
	// Title
	$titulo = "UNIVERSIDAD TÉCNICA LUIS VARGAS TORRES DE ESMERALDAS";
		
	$this->Cell(0, 10, $titulo, 0, false, 'C', 0, '', 0);
	$this->Cell(0, 20, "Vicerectorado Académico                                                             . ", 0, false, 'R', 0, '', 0);
	$this->Cell(0, 30, "Esmeraldas Ecuador                                                                 . ", 0, false, 'R', 0, '', 0);
	
	$imager_file = K_PATH_IMAGES.'logo.jpg';
	$imagel_file = K_PATH_IMAGES.'sello_Ecuador.png';
	$this->Image($imagel_file, 15, 5, 20, 20, '', '', '', false, 300, '', false, false, 0);
	$this->Image($imager_file, 175, 5, 20, 20, '', '', '', false, 300, '', false, false, 0);
    }

    // Page footer
	
    public function Footer() {
	// Position at 15 mm from bottom
		$texto = "F. :__________________________";
	$this->SetY(-15);
	// Set font
	$this->SetFont('helvetica', 'I', 8);
	$this->Cell(0, 1, $texto, 0, false, 'C', 0, '', 0, false, 'T', 'M');
	//$pdf->write1DBarcode($this->$iddocente, 'C39', '', '', '', 5, 0.2, '', 'N');
	// Page number
	$this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
	
}
