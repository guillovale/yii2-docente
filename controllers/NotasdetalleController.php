<?php

namespace app\controllers;

use Yii;
use app\models\NotasDetalle;
use app\models\Notasalumnoasignatura;
use app\models\NotasDetalleSearch;
use app\models\CursoOfertado;
use app\models\LibretaCalificacion;
use app\models\DetalleMatricula;
use app\models\Asignatura;
use app\models\Informacionpersonald;
use app\models\Periodolectivo;
use app\models\Carrera;
use app\models\Componentescalificacion;
use app\models\Parametroscalificacion;
use app\models\Configuracion;
use app\models\Docenteperasig;
use app\models\ExtensionDocente;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\db\Query;


require_once(__DIR__ . '/../vendor/tcpdf/tcpdf.php');
//usar clase TCPDF
use TCPDF;
/**
 * NotasdetalleController implements the CRUD actions for NotasDetalle model.
 */
class NotasdetalleController extends Controller
{
    /**
     * @inheritdoc
     */
	//public $compA, $compB, $compC, $compEx, $compAs, $compT;

    public function behaviors()
    {
        return [


		'access' => [
                'class' => AccessControl::className(),
                'only' => ['delete','update', 'create', 'index', 'publicar'],
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
     * Lists all NotasDetalle models.
     * @return mixed
     */
    public function actionIndex1($idcurso)
    {
		$publicar = 0;
		$cursomodel = $this->findCurso($idcurso);
		$usuario = Yii::$app->user->identity;
		$periodo = $cursomodel->periodo;
		$carrera = $cursomodel->detallemalla->malla->carrera;
		$asignatura = $cursomodel->detallemalla->asignatura;
		$detallemalla = $cursomodel->detallemalla;
		$docente = $cursomodel->docente;
		if (!$cursomodel)		
			$query = $this->getHistoriconotas($idper, $iddocente, $idcarr, $idasig, $nivel, $paralelo);

		if ($usuario && $cursomodel ) {
			$hoy  = date('Y-m-d');
			if ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->examsupletorio_fin)
				$publicar = 1;
			if ($carrera)
				if ($carrera->optativa == 1)
					$publicar = 1;
			if ($periodo->StatusPerLec == 0)
				$publicar = 1;
		
			$ca = configuracion::find()->where(['dato'=> 'CA'])->one();
			$cb = configuracion::find()->where(['dato'=> 'CB'])->one();
			$cc = configuracion::find()->where(['dato'=> 'CC'])->one();
			$ex = configuracion::find()->where(['dato'=> 'EX'])->one();
			$as = configuracion::find()->where(['dato'=> 'AS'])->one();
			$ct = configuracion::find()->where(['dato'=> 'CT'])->one();
			$compA = $ca?$ca->valor/100:0;
			$compB = $cb?$cb->valor/100:0;
			$compC = $cc?$cc->valor/100:0;
			$compEx = $ex?$ex->valor/100:0;
			$compAs = $as?$as->valor/100:0;
			$compT = $ct?$ct->valor/100:0;

			$this->view->params['publicar'] = $publicar;
			$this->view->params['idper'] = $periodo->idper;
			$this->view->params['idcarr'] = $carrera?$carrera->idCarr:'';
			$this->view->params['idasig'] = $asignatura?$asignatura->IdAsig:'';
			$this->view->params['periodo'] = $periodo->DescPerLec;
			$this->view->params['carrera'] = $carrera?$carrera->NombCarr:'';
			$this->view->params['asignatura'] = $asignatura?$asignatura->NombAsig:'';
			$this->view->params['nivel'] = $detallemalla?$detallemalla->nivel:'';
			$this->view->params['paralelo'] = $cursomodel->paralelo;
			$this->view->params['idcurso'] = $cursomodel->id;
			$this->view->params['docente'] = $docente?
							($docente->ApellInfPer . ' ' . $docente->ApellMatInfPer . ' ' . $docente->NombInfPer):'';
			$this->view->params['cedula'] = $docente?$docente->CIInfPer:'';
			$this->view->params['ca'] = $compA;
			$this->view->params['cb'] = $compB;
			$this->view->params['cc'] = $compC;
			$this->view->params['ex'] = $compEx;
			$this->view->params['as'] = $compAs;
			$this->view->params['ct'] = $compT;
						
			$query = $this->getQuerynotas($cursomodel->idper, $cursomodel->id);
		}
		
			

			//echo var_dump($query->all()); exit;
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => ['pagesize' => 80,],
			]);

			$searchModel = $dataProvider->getModels();


		    return $this->render('index', [
		        'searchModel' => $searchModel,
		        'dataProvider' => $dataProvider,
				#'matriculas' => $matriculas,
		    ]);
		
		#return $this->redirect(Yii::$app->request->referrer);	
    }

	public function actionIndex($idcurso)
    {
		$publicar = 0;
		$cursomodel = $this->findCurso($idcurso);
		$usuario = Yii::$app->user->identity;
		$periodo = $cursomodel->periodo;
		$carrera = $cursomodel->detallemalla->malla->carrera;
		$asignatura = $cursomodel->detallemalla->asignatura;
		$detallemalla = $cursomodel->detallemalla;
		$docente = $cursomodel->docente;
		if (!$cursomodel)		
			$query = $this->getHistoriconotas($idper, $iddocente, $idcarr, $idasig, $nivel, $paralelo);

		if ($usuario && $cursomodel ) {
			$hoy  = date('Y-m-d');
			$extensiondocente = ExtensionDocente::find()
				->where(['idcurso' => $cursomodel->id])
				->andwhere(['<=','fecha_inicio', $hoy])
				->andwhere(['>=','fecha_fin', $hoy])
				->one();
			if ( ($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin) && 
					$periodo->StatusPerLec == 1 )
				$publicar = 1;
			elseif ($periodo->StatusPerLec != 1)
				$publicar = 1;
			
			$ca = configuracion::find()->where(['dato'=> 'CA'])->one();
			$cb = configuracion::find()->where(['dato'=> 'CB'])->one();
			$cc = configuracion::find()->where(['dato'=> 'CC'])->one();
			$ex = configuracion::find()->where(['dato'=> 'EX'])->one();
			$as = configuracion::find()->where(['dato'=> 'AS'])->one();
			$ct = configuracion::find()->where(['dato'=> 'CT'])->one();
			$compA = $ca?$ca->valor/100:0;
			$compB = $cb?$cb->valor/100:0;
			$compC = $cc?$cc->valor/100:0;
			$compEx = $ex?$ex->valor/100:0;
			$compAs = $as?$as->valor/100:0;
			$compT = $ct?$ct->valor/100:0;

			$this->view->params['publicar'] = $publicar;
			$this->view->params['idper'] = $periodo->idper;
			$this->view->params['idcarr'] = $carrera?$carrera->idCarr:'';
			$this->view->params['idasig'] = $asignatura?$asignatura->IdAsig:'';
			$this->view->params['periodo'] = $periodo->DescPerLec;
			$this->view->params['carrera'] = $carrera?$carrera->NombCarr:'';
			$this->view->params['asignatura'] = $asignatura?$asignatura->NombAsig:'';
			$this->view->params['nivel'] = $detallemalla?$detallemalla->nivel:'';
			$this->view->params['paralelo'] = $cursomodel->paralelo;
			$this->view->params['idcurso'] = $cursomodel->id;
			$this->view->params['docente'] = $docente?
							($docente->ApellInfPer . ' ' . $docente->ApellMatInfPer . ' ' . $docente->NombInfPer):'';
			$this->view->params['cedula'] = $docente?$docente->CIInfPer:'';
			$this->view->params['ca'] = $compA;
			$this->view->params['cb'] = $compB;
			$this->view->params['cc'] = $compC;
			$this->view->params['ex'] = $compEx;
			$this->view->params['as'] = $compAs;
			$this->view->params['ct'] = $compT;
						
			$query = $this->getQuerynotas($cursomodel->idper, $cursomodel->id);
		
		
			

			//echo var_dump($query->all()); exit;
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => ['pagesize' => 80,],
			]);

			$searchModel = $dataProvider->getModels();


		    return $this->render('index', [
		        'searchModel' => $searchModel,
		        'dataProvider' => $dataProvider,
				'extensiondocente' => $extensiondocente,
		    ]);
		}
		
		return $this->redirect(Yii::$app->request->referrer);	
    }

    /**
     * Displays a single NotasDetalle model.
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
     * Creates a new NotasDetalle model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($idcurso)
    {
		$usuario = Yii::$app->user->identity;
		$grabar = 0;
		$sigla = [];
		$fecha = date('Y-m-d');
		$hemisemestre = [];
		$modelcurso = $this->findCurso($idcurso);
		$extdmodel = ExtensionDocente::find()
				->where(['idcurso' => $idcurso])
				->andwhere(['<=','fecha_inicio', $fecha])
				->andwhere(['>=','fecha_fin', $fecha])
				->one();
		#$idperiodo = $modelcurso?$modelcurso->idper:0;
		$periodo = Periodolectivo::find()
			->where(['StatusPerLec' => 1])
			->one();

		if ($usuario && $modelcurso && $periodo) {
			$hoy  = date('Y-m-d');
			$carrera = isset($modelcurso->detallemalla->malla->carrera->idCarr)?
					$modelcurso->detallemalla->malla->carrera->idCarr:'';
		
			$tipo_ingreso = isset($modelcurso->detallemalla->malla->detalle)?
						$modelcurso->detallemalla->malla->detalle:'';

			$carreraop = isset($modelcurso->detallemalla->malla->carrera->optativa)?
						$modelcurso->detallemalla->malla->carrera->optativa:0;

			if ($modelcurso->fecha_fin >= $hoy) {
				if ( ('SNNA' == substr($tipo_ingreso, 0, 4)) && $hoy >= $periodo->finiciohemi1 ) {
					$sigla = ['N','M'];
					$hemisemestre = [0=>0];
					$grabar = 1;
				}			

				elseif ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1) {
					$sigla = ['A','B','C','X','T'];
					$grabar = 1;
					$hemisemestre = [1=>1];
				}
				# elseif ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2) {
				elseif ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2) {
					$sigla = ['A','B','C','X','T'];
					$grabar = 1;
					$hemisemestre = [2=>2];
				}
				elseif ($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin) {
					$grabar = 1;
					$hemisemestre = [0=>0];
					$sigla = ['R'];	
				}
				#elseif ($carreraop == 1) {
				#	$hemisemestre = [1=>1, 2=>2, 0=>0];
				#	$sigla = ['A','B','C','X','T', 'R'];
				#	$grabar = 1;
				#}
				if ($carreraop == 1 ) {
					$hemisemestre = [1=>1, 2=>2, 0=>0];
					$sigla = ['A','B','C','X','T', 'R'];
					$grabar = 1;
				}
			}
		}
		
		

		if ($usuario && $modelcurso && $extdmodel) {
			if ('SNNA' == substr($tipo_ingreso, 0, 4)) {
				$sigla = ['N','M'];
				$hemisemestre = [0=>0];
				$grabar = 1;
			}
			else {	
				$hemisemestre = [1=>1, 2=>2, 0=>0];
				$sigla = ['A','B','C','X','T', 'R'];
				$grabar = 1;
			}
		}

		#if ($carrera == '056' || $carrera == '197' ) {
		#	$grabar = 0;
		#}
		#&& $publicar == 0
		
		if ($grabar == 1 && $usuario && $modelcurso) {

			//$usuario = Yii::$app->user->identity;
			$libretaModel = new LibretaCalificacion();
			$cedula = $usuario->CIInfPer;
			$libretaModel['iddocente'] = $cedula;
			$libretaModel['fecha'] = date("Y-m-d H:i:s");
			$libretaModel['idper'] = $modelcurso->idper;
			$libretaModel['idcurso'] = $modelcurso->id;
			#$libretaModel['iddocenteperasig'] = $iddpa;
			#$libretaModel['hemisemestre'] = $hemisemestre;

			$detallematricula = DetalleMatricula::find()
							->select('detalle_matricula.id, factura.cedula, idfactura, ApellInfPer, ApellMatInfPer, NombInfPer')
							//->from(['detalle_matricula'])
							->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
							->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
							->where(['idcurso'=>$idcurso, 'detalle_matricula.estado'=> 1])
							#->where(['idcarr'=>$idcarr, 'idasig' => $idasig, 'nivel' => $nivel, 
							#	'paralelo' => $paralelo, 'idper'=>$idper])
							->orderBy(['ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC])
							->all();

			$componente = ArrayHelper::map(componentescalificacion::find()
							->select('idcomponente, componente')
							->asArray()
							->all(), 'idcomponente', 'componente');
			$parametro = ArrayHelper::map(parametroscalificacion::find()
							->select('idparametro, parametro')
							->where(['in', 'sigla', $sigla])
							->asArray()
							->all(), 'idparametro', 'parametro');
			#$asignatura = Asignatura::find()
			#							->where(['idAsig'=> $idasig])
			#							->one();
		
			//$fecha = $periodo?$periodo->f
			$this->view->params['hemisemestre'] = $hemisemestre;
			$this->view->params['componente'] = $componente;
			$this->view->params['parametro'] = $parametro;
			$this->view->params['asignatura'] = $modelcurso->detallemalla->asignatura->NombAsig;
			$this->view->params['nivel'] = $modelcurso->detallemalla->nivel;
			$this->view->params['paralelo'] = $modelcurso->paralelo;
			$cont = 0;//count(Yii::$app->request->post('Setting', []));
		
			$recuperacion = false;
			//$settings[] = new NotasDetalle();
			if ($detallematricula){
			
				foreach ($detallematricula as $detalle) {
					#if ($hemi == 0 ) {
					#	$nota = $this->getNotas($detalle->id)?$this->getNotas($detalle->id):0;
						//echo var_dump($this->getNota($detalle->id), $nota[0]); exit;
					#	if ( $nota['nota'] >= 5 && $nota['nota'] < 7 ) {
							$settings[] = new NotasDetalle();
							$settings[$cont]['nota'] = 0;
							$settings[$cont]['cedula'] = $detalle->getAlumno();
							$settings[$cont]['iddetallematricula'] = $detalle->id;
							$cont += 1;
					#	}	
					#}
					#elseif ($hemi == 1 || $hemi == 2) {
					#	$settings[] = new NotasDetalle();
					#	$settings[$cont]['nota'] = 0;
					#	$settings[$cont]['cedula'] = $detalle->getAlumno();
					#	$settings[$cont]['iddetallematricula'] = $detalle->id;
					#	$cont += 1;
					#}
				}
			}
			#else
				#$settings[] = new NotasDetalle();
		
		
		
			if ($libretaModel->load(Yii::$app->request->post())) {
				#echo var_dump($grabar, ' ', $libretaModel['hemisemestre']); exit;
				$peso = 0;
				if ($libretaModel->save() && $detallematricula) {
					$idlibreta = $libretaModel->id;
					#$peso = round($libretaModel->componente0->idParam->escala /100, 3);
					#if ($libretaModel->componente0->idParam->sigla == 'A' || $libretaModel->componente0->idParam->sigla == 'B' 
					#	|| $libretaModel->componente0->idParam->sigla == 'C') {
					#	$peso = round($libretaModel->componente0->idParam->escala * 70/10000, 3);
					#}
					#else {
						$peso = round($libretaModel->componente0->idParam->escala /100, 3);
					#}
					
					foreach ($settings as $detalle) {
						$detalle['idlibreta'] = $idlibreta;
						$detalle['fecha_crea'] = date("Y-m-d H:i:s");
					}
			
					if (NotasDetalle::loadMultiple($settings, Yii::$app->request->post()) && 
						NotasDetalle::validateMultiple($settings)) {
						foreach ($settings as $setting) {
							if ($setting->nota >= 0 && $setting->nota <= 10)
								$setting->peso = $peso;
								$setting->save(false);
						}
						//return $this->redirect('/libretacalificacion/index');
					}
				}
				return Yii::$app->response->redirect(['libretacalificacion/index', 'idcurso'=> $idcurso]);
			}

			if (!isset($settings))
				$settings = [];		
			//if ($libretaModel->getErrors()) {echo var_dump($libretaModel->getErrors()); exit;}
				
			return $this->render('create', ['settings' => $settings, 'libretaModel' => $libretaModel]);
		}
		
		return $this->redirect(Yii::$app->request->referrer);	
	
    }

	public function actionCreamasivo($idcurso)
    {
		$usuario = Yii::$app->user->identity;
		$grabar = 0;
		$sigla = [];
		$fecha = date('Y-m-d');
		$hemisemestre = [];
		$modelcurso = $this->findCurso($idcurso);
		$extdmodel = ExtensionDocente::find()
				->where(['idcurso' => $idcurso])
				->andwhere(['<=','fecha_inicio', $fecha])
				->andwhere(['>=','fecha_fin', $fecha])
				->one();
		#$idperiodo = $modelcurso?$modelcurso->idper:0;
		$periodo = Periodolectivo::find()
			->where(['StatusPerLec' => 1])
			->one();
		
		if ($usuario && $modelcurso && $periodo) {
			$hoy  = date('Y-m-d');
			$carrera = isset($modelcurso->detallemalla->malla->carrera->idCarr)?
					$modelcurso->detallemalla->malla->carrera->idCarr:'';
		
			$tipo_ingreso = isset($modelcurso->detallemalla->malla->detalle)?
						$modelcurso->detallemalla->malla->detalle:'';

			$carreraop = isset($modelcurso->detallemalla->malla->carrera->optativa)?
						$modelcurso->detallemalla->malla->carrera->optativa:0;

			if ($modelcurso->fecha_fin >= $hoy) {
				if ( ('SNNA' == substr($tipo_ingreso, 0, 4)) && $hoy >= $periodo->finiciohemi1 ) {
					$sigla = ['N','M'];
					$hemisemestre = [0];
					$grabar = 1;
				}			

				elseif ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1) {
					$sigla = ['A','B','C','X','T'];
					$grabar = 1;
					$hemisemestre = [1];
				}
				# elseif ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2) {
				elseif ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2) {
					$sigla = ['A','B','C','X','T'];
					$grabar = 1;
					$hemisemestre = [2];
				}
				elseif ($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin) {
					$grabar = 1;
					$hemisemestre = [0];
					$sigla = ['R'];	
				}
				#elseif ($carreraop == 1) {
				#	$hemisemestre = [1=>1, 2=>2, 0=>0];
				#	$sigla = ['A','B','C','X','T', 'R'];
				#	$grabar = 1;
				#}
				if ($carreraop == 1 ) {
					$hemisemestre = [1, 2, 0];
					$sigla = ['A','B','C','X','T', 'R'];
					$grabar = 1;
				}
			}
		}
		
		

		if ($usuario && $modelcurso && !$extdmodel) {
			if ('SNNA' == substr($tipo_ingreso, 0, 4)) {
				$sigla = ['N','M'];
				$hemisemestre = [0];
				$grabar = 1;
			}
			else {	
				$hemisemestre = [1, 2, 0];
				$sigla = ['A','B','C','X','T', 'R'];
				$grabar = 1;
			}
		}

		#if ($carrera == '056' || $carrera == '197' ) {
		#	$grabar = 0;
		#}
		#&& $publicar == 0
		#echo var_dump($sigla); exit;
		
		if ($grabar == 1 && $usuario && $modelcurso) {

			$detallematricula = DetalleMatricula::find()
							->select('id')
							->where(['idcurso'=>$idcurso])
							->all();
		
			if ($detallematricula){
				foreach ($hemisemestre as $hemi) {
					foreach ($sigla as $sig) {
						
						$idcomponente = Componentescalificacion::find()
							->select('idcomponente')
							->leftJoin('parametroscalificacion', 'parametroscalificacion.idparametro = 
										componentescalificacion.idparametro')
							->where(['parametroscalificacion.sigla'=>$sig])
							->one();
						
						if (!$idcomponente) {
							return $this->redirect(Yii::$app->request->referrer);
						}
						
						$libreta = LibretaCalificacion::find()
									->leftJoin('componentescalificacion', 'componentescalificacion.idcomponente = 
										libreta_calificacion.idcomponente')
									->leftJoin('parametroscalificacion', 'parametroscalificacion.idparametro = 
										componentescalificacion.idparametro')
									->where(['idcurso'=> $modelcurso->id, 'hemisemestre' => $hemi,
										'parametroscalificacion.sigla' => $sig])
									->one();
						#if ($sig == 'B') {echo var_dump($libreta); exit;}
						#if ($sigla == 'B') {echo var_dump($libreta, $idcomponente); exit;}
						if ( !$libreta && (  ($hemi == 1 || $hemi == 2) && $sig != 'R'  || ( $hemi == 0 && $sig == 'R')  )  {
																
							#if ($sig == 'R') {
							#	echo var_dump($sig, $libreta, $idcomponente); 
							#	exit;
							# }
							$libretaModel = new LibretaCalificacion();
							$cedula = $usuario->CIInfPer;
							$libretaModel['iddocente'] = $cedula;
							$libretaModel['fecha'] = date("Y-m-d H:i:s");
							$libretaModel['idper'] = $modelcurso->idper;
							$libretaModel['idcurso'] = $modelcurso->id;
							$libretaModel['hemisemestre'] = $hemi;
							$libretaModel['idcomponente'] = $idcomponente->idcomponente;
							#$libretaModel['iddocenteperasig'] = null;
							#$libretaModel['idparametro'] = null;
							if ( !$libretaModel->save() ) {
								return $this->redirect(Yii::$app->request->referrer);
							}
							$idlibreta = $libretaModel->id;
							$peso = round($libretaModel->componente0->idParam->escala /100, 3);
							foreach ($detallematricula as $detalle) {
								$nota = new NotasDetalle();
								$nota->idlibreta = $idlibreta;
								$nota->iddetallematricula = $detalle->id;
								$nota->fecha_crea = date("Y-m-d H:i:s");
								$nota->nota = 0;
								$nota->peso = $peso;
								$nota->save();
							}
						}
					}
				}
				
				return Yii::$app->response->redirect(['libretacalificacion/index', 'idcurso'=> $idcurso]);
			}
		}
		
		return $this->redirect(Yii::$app->request->referrer);	
	
    }

	public function actionCreaasistencia($idcurso)
    {
		$grabar = 0;
		$hemi = 0;
		$sigla = ['T'];
		$modelcurso = $this->findCurso($idcurso);
		$idper = $modelcurso?$modelcurso->idper:0;
		$periodo = Periodolectivo::find()
			->where(['idPer'=>$idper, 'StatusPerLec' => 1])
			->one();
		$componente = Componentescalificacion::find()
                ->where(['componente' => 'Asistencia'])
                ->one();
		#$publicado = Docenteperasig::find()
		#							->where(['dpa_id'=> $iddpa])->one();
		#$publicar = $publicado?$publicado->publicar:0;
		if ($periodo) {
			$hoy  = date('Y-m-d');
			
			if ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1) {
				$grabar = 1;
				$hemi = 1;
			}
			elseif ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2) {
				$grabar = 1;
				$hemi = 2;
			}
			else {
				$grabar = 0;
			}
		}
		
		#&& $publicar == 0
		$usuario = Yii::$app->user->identity;
		
		if ($grabar == 1 && $usuario && $componente ) {

			//$usuario = Yii::$app->user->identity;
			$libretaModel = new LibretaCalificacion();
			$cedula = $usuario->CIInfPer;
			$libretaModel['iddocente'] = $cedula;
			$libretaModel['fecha'] = date("Y-m-d H:i:s");
			$libretaModel['idper'] = $periodo?$periodo->idper:0;
			$libretaModel['idcurso'] = $idcurso;
			#$libretaModel['iddocenteperasig'] = $iddpa;
			$libretaModel['hemisemestre'] = $hemi;
			$libretaModel['idcomponente'] = $componente?$componente->idcomponente:0;

			$detallematricula = DetalleMatricula::find()
							->select('detalle_matricula.id, factura.cedula, idfactura, ApellInfPer, ApellMatInfPer, NombInfPer')
							//->from(['detalle_matricula'])
							->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
							->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
							->where(['idcurso'=>$idcurso, 'detalle_matricula.estado'=> 1])
							#->where(['idcarr'=>$idcarr, 'idasig' => $idasig, 'nivel' => $nivel, 
							#	'paralelo' => $paralelo, 'idper'=>$idper])
							->orderBy(['ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC])
							->all();

			$componente = ArrayHelper::map(componentescalificacion::find()
							->select('idcomponente, componente')
							->asArray()
							->all(), 'idcomponente', 'componente');
			$parametro = ArrayHelper::map(parametroscalificacion::find()
							->select('idparametro, parametro')
							->where(['in', 'sigla', $sigla])
							->asArray()
							->all(), 'idparametro', 'parametro');
			$asignatura = Asignatura::find()
										->where(['idAsig'=> $modelcurso->detallemalla->idasignatura])
										->one();
		
			//$fecha = $periodo?$periodo->f
			$this->view->params['componente'] = $componente;
			$this->view->params['parametro'] = $parametro;
			$this->view->params['asignatura'] = $asignatura?$asignatura->NombAsig:'';
			$this->view->params['nivel'] = $modelcurso->detallemalla->nivel;
			$this->view->params['paralelo'] = $modelcurso->paralelo;
			$cont = 0;//count(Yii::$app->request->post('Setting', []));
		
			$recuperacion = false;
			//$settings[] = new NotasDetalle();
			if ($detallematricula){
			
				foreach ($detallematricula as $detalle) {
					if ($hemi == 1 || $hemi == 2) {
						$settings[] = new NotasDetalle();
						$settings[$cont]['nota'] = 1;
						$settings[$cont]['cedula'] = $detalle->getAlumno();
						$settings[$cont]['iddetallematricula'] = $detalle->id;
						$cont += 1;
					}
				}
			}
			#else
				#$settings[] = new NotasDetalle();
		
		
			if ($grabar == 1 && $libretaModel->load(Yii::$app->request->post())) {
				//echo var_dump($grabar, ' ', $libretaModel['hemisemestre']); exit;
				if ($libretaModel->save() && $detallematricula) {
					$idlibreta = $libretaModel->id;
					foreach ($settings as $detalle) {
						$detalle['idlibreta'] = $idlibreta;
						$detalle['fecha_crea'] = date("Y-m-d H:i:s");
					}
			
					if (NotasDetalle::loadMultiple($settings, Yii::$app->request->post()) && 
						NotasDetalle::validateMultiple($settings)) {
						foreach ($settings as $setting) {
							if ($setting->nota >= 0 && $setting->nota <= 10)
								$setting->save(false);
						}
						//return $this->redirect('/libretacalificacion/index');
					}
				}
				#echo var_dump($libretaModel->getErrors()); exit;
				return Yii::$app->response->redirect(['libretacalificacion/index', 'idcurso'=> $idcurso]);
			}

			if (!isset($settings))
				$settings = [];		
			//if ($libretaModel->getErrors()) {echo var_dump($libretaModel->getErrors()); exit;}
				
			return $this->render('creaasistencia', ['settings' => $settings, 'libretaModel' => $libretaModel]);
		}
		
		return $this->redirect(Yii::$app->request->referrer);	
	
    }

    /**
     * Updates an existing NotasDetalle model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$usuario = Yii::$app->user->identity;
		$model = $this->findModel($id);
		$hoy  = date('Y-m-d');
		#$idper = $model?$model->idLibreta->idper:0;
		$periodo = Periodolectivo::find()
			->where(['StatusPerLec' => 1])
			->one();
		$grabar = 0;
		$hemi = -1;
		$publicar = 0;
		
		if ($usuario && $model) {
			$carrera = $model->iddetalle->idCarr0->idCarr?$model->iddetalle->idCarr0->idCarr:'';
			$tipo_ingreso = isset($model->idLibreta->curso->detallemalla->malla->detalle)?
						$model->idLibreta->curso->detallemalla->malla->detalle:'';
			$carreraop = isset($model->iddetalle->idCarr0->optativa)?
						$model->iddetalle->idCarr0->optativa:0;
			
			$extdmodel = ExtensionDocente::find()
				->where(['idcurso' => $model->idLibreta->curso->id])
				->andwhere(['<=','fecha_inicio', $hoy])
				->andwhere(['>=','fecha_fin', $hoy])
				->one();
			$model['fecha'] = date("Y-m-d H:i:s");
			$this->view->params['alumno'] = $model?$model->getAlumno():'';
			$this->view->params['idasig'] = $model?$model->getIdasig():'';

			if ( $periodo && ($model->idLibreta->curso->fecha_fin >= $hoy) ) {
			
				if ( ('SNNA' == substr($tipo_ingreso, 0, 4)) && ($hoy >= $periodo->finiciohemi1) ) {	
					$grabar = 1;
				}	

				elseif ($model->hemisemestre == 1 && ($hoy >= $periodo->finiciohemi1 && $hoy <= $periodo->ffinhemi1)) {
					$grabar = 1;
				}

				elseif ($model->hemisemestre == 2 && ($hoy >= $periodo->finiciohemi2 && $hoy <= $periodo->ffinhemi2)) {
					$grabar = 1;
				}
				elseif ($model->hemisemestre == 0 && 
						($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin) ) {
					$grabar = 1;
				}
				
					
				if ($carreraop == 1 ) {
						#$hemisemestre = [1=>1, 2=>2, 0=>0];
						#$sigla = ['A','B','C','X','T', 'R'];
					$grabar = 0;
				}
			}
			if ($extdmodel) {
						#$hemisemestre = [1=>1, 2=>2, 0=>0];
						#$sigla = ['A','B','C','X','T', 'R'];
				$grabar = 1;
			}
			/*if ($carrera == '056' || $carrera == '197' ) {
				$grabar = 0;
			}*/
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			if ($grabar == 1 && $publicar == 0) {
				if ($model->load(Yii::$app->request->post())) {
					$peso = 0;
					#if ($model->idLibreta->componente0->idParam->sigla == 'A' 
					#	|| $model->idLibreta->componente0->idParam->sigla == 'B' 
					#	|| $model->idLibreta->componente0->idParam->sigla == 'C') {
					#	$peso = round($model->idLibreta->componente0->idParam->escala * 70/10000, 3);
					#}
					#else {
						$peso = round($model->idLibreta->componente0->idParam->escala /100, 3);
					#}
					$model->peso = $peso;
					$model->save();
					return $this->redirect(['libretacalificacion/view', 'id' => $model->idlibreta]);
				}

				return $this->render('update', [
	           		'model' => $model,
		        ]);
			}
			else {
	      		return $this->redirect(Yii::$app->request->referrer);
	    	}	
		}

		return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Deletes an existing NotasDetalle model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

	public function actionAsistencia($idcurso)
    {
		$modelcurso = $this->findCurso($idcurso);
		$idper = $modelcurso?$modelcurso->idper:0;
		$periodo = Periodolectivo::find()
				->where(['idPer'=>$idper])
				->one();
		$carrera = Carrera::find()
				->where(['idCarr'=>$modelcurso->detallemalla->malla->idcarrera])
				->one();
		$asignatura = Asignatura::find()
				->where(['idAsig'=>$modelcurso->detallemalla->idasignatura])
				->one();

		if ($periodo) {
			$hoy  = date('Y-m-d');
			if ($hoy >= $periodo->examsupletorio_ini && $hoy <= $periodo->examsupletorio_fin)
				$publicar = 1;
		}
		$as = configuracion::find()->where(['dato'=> 'AS'])->one();
		$compAs = $as?$as->valor/100:0;


		$this->view->params['publicar'] = 0;
		$this->view->params['idper'] = $idper;
		$this->view->params['idcarr'] = $modelcurso->detallemalla->malla->idcarrera;
		$this->view->params['idasig'] = $modelcurso->detallemalla->idasignatura;
		$this->view->params['periodo'] = $periodo?$periodo->DescPerLec:'';
		$this->view->params['carrera'] = $carrera?$carrera->NombCarr:'';
		$this->view->params['asignatura'] = $asignatura?$asignatura->NombAsig:'';
		$this->view->params['nivel'] = $modelcurso->detallemalla->nivel;
		$this->view->params['paralelo'] = $modelcurso->paralelo;
		$this->view->params['idcurso'] = $idcurso;
		
		//echo var_dump($compA, $compB, $compC); exit;
		$identity = Yii::$app->user->identity;
		    
		$docente = Informacionpersonald::find()->where(['CIInfPer'=>$identity->CIInfPer])->one();
		if ($docente) {
				$this->view->params['docente'] = $docente->ApellInfPer . ' ' . $docente->ApellMatInfPer . ' ' . $docente->NombInfPer;
				$this->view->params['cedula'] = $identity->CIInfPer;
		}
	
			

		$query = $this->getAsistencia($idcurso);

		//print_r(array_keys($query));exit;

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['pagesize' => 80,],
		]);

		//$searchModel = $dataProvider->getModels();

		//$query->select('iddetallematricula');
		//$idmatriculas = ArrayHelper::getColumn($query->all(), 'iddetallematricula');
		//$value = ArrayHelper::getValue($query->all(), 'iddetallematricula');
		/* $idmatriculas = $query->all();
		$matriculas = '';
		if ($idmatriculas) {
			foreach ($idmatriculas as $idmatricula) {
				$matriculas = $matriculas.';'.$idmatricula['iddetallematricula'];

			}
			//$matriculas = trim($matriculas);
		}
		*/
		//echo var_dump($searchModel); exit;
		

		//**************************************
        //$searchModel = new NotasDetalleSearch();
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('asistencia', [
            //'searchModel' => $searchModel,
			'query' => $query,
            'dataProvider' => $dataProvider,
			//'matriculas' => $matriculas,
        ]);
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
		$porciones = explode(";", $id);
		$nivel = ($porciones[0]?$porciones[0]:'');
		$snna = ($porciones[1]?$porciones[1]:'');
		$rest = substr($snna, 0, 4); 
		if ($nivel == 0 && $rest == 'SNNA' ) {
			$sigla = ['M', 'N'];
		}
		elseif ($nivel == 0) {
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

	public function getNotas($idmatricula, $idcurso)
    {
		$nota1 = 0;
		$nota2 = 0;
		$ca = configuracion::find()->where(['dato'=> 'CA'])->one();
		$cb = configuracion::find()->where(['dato'=> 'CB'])->one();
		$cc = configuracion::find()->where(['dato'=> 'CC'])->one();
		$ex = configuracion::find()->where(['dato'=> 'EX'])->one();
		$as = configuracion::find()->where(['dato'=> 'AS'])->one();
		$ct = configuracion::find()->where(['dato'=> 'CT'])->one();
		$compA = $ca?$ca->valor/100:0;
		$compB = $cb?$cb->valor/100:0;
		$compC = $cc?$cc->valor/100:0;
		$compEx = $ex?$ex->valor/100:0;
		$compAs = $as?$as->valor/100:0;
		$compT = $ct?$ct->valor/100:0;

		$subquery = NotasDetalle::find()
					->select(['factura.cedula, idlibreta, iddetallematricula, 
						hemisemestre, idfactura, libreta_calificacion.idcomponente, nota, 
						
						IF(hemisemestre = 1 && sigla = "A", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompA1,
						IF(hemisemestre = 1 && sigla = "B", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompB1,
						IF(hemisemestre = 1 && sigla = "C", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompC1,
						IF(hemisemestre = 1 && sigla = "X", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS EXM1,
						IF(hemisemestre = 1 && sigla = "T", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Ast1,
						IF(hemisemestre = 2 && sigla = "A", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompA2,
						IF(hemisemestre = 2 && sigla = "B", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompB2,
						IF(hemisemestre = 2 && sigla = "C", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompC2,
						IF(hemisemestre = 2 && sigla = "X", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS EXM2,
						IF(hemisemestre = 2 && sigla = "T", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Ast2,
						IF(hemisemestre = 0 && sigla = "R", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Rec
						'])
					->leftJoin('libreta_calificacion', 'libreta_calificacion.id = notas_detalle.idlibreta')
					->leftJoin('componentescalificacion', 'componentescalificacion.idcomponente = libreta_calificacion.idcomponente')
					->leftJoin('parametroscalificacion', 'parametroscalificacion.idparametro = componentescalificacion.idparametro')
					->leftJoin('detalle_matricula', 'detalle_matricula.id = notas_detalle.iddetallematricula')
					->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
					#->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
					->where(['iddetallematricula'=>$idmatricula, 'libreta_calificacion.idcurso'=>$idcurso])
					->groupBy(['libreta_calificacion.hemisemestre','parametroscalificacion.sigla'])
					->orderBy(['libreta_calificacion.hemisemestre'=>SORT_ASC, 'parametroscalificacion.idparametro'=>SORT_ASC]);
		$query = new Query();
		$query->select(['*'])
			->addSelect(['sum(c.CompA1) as A1, sum(c.CompB1) as B1, sum(c.CompC1) as C1, sum(c.EXM1) as Ex1, sum(c.Ast1) as As1,
						sum(c.CompA2) as A2, sum(c.CompB2) as B2, sum(c.CompC2) as C2, sum(c.EXM2) as Ex2, sum(c.Ast2) as As2,
						sum(c.Rec) as Suf
						'])
			->from(['c' => $subquery]);
		$row = $query->one();
		$estado = '';
		//$recuperacion = 0;
		//$sumanotas = 0;
		$notas = [];
		$aprobada = 0;
		if (count($row)) { 
			$nota1 = ( ($row["A1"]*$compA + $row["B1"]*$compB + $row["C1"]*$compC)*$compT + $row["Ex1"]*$compEx );
			$nota2 = ( ($row["A2"]*$compA + $row["B2"]*$compB + $row["C2"]*$compC)*$compT + $row["Ex2"]*$compEx );
			$asis1 = ($row["As1"] >=0 && $row["As1"] <=10)?$row["As1"]:$row["As1"];
			$asis2 = ($row["As2"] >=0 && $row["As2"] <=10)?$row["As2"]:$row["As2"];
			$promedionota = round((round($nota1) + round($nota2))/2, 2);
			$promedioasistencia = round((round($asis1) + round($asis2))/2, 2);
			$recp = round($row["Suf"]?$row["Suf"]:0);
			if ( $promedionota >= 7 && ($promedioasistencia >= 7 && $promedioasistencia <= 10) ) {
				$estado = 'APROBADA';
				$aprobada = 1;	
			}
			elseif ( $promedionota >= 5 && $promedionota < 7 && ($promedioasistencia >= 7 && $promedioasistencia <= 10) )
				if ($promedionota*2 + $recp >= 20){
					$estado = 'APROBADA';
					$promedionota = 7;
					$aprobada = 1;
				}
				else {
					$estado = 'REPROBADA';
					$aprobada = 0;
				}
			else {
				$estado = 'REPROBADA';
				$aprobada = 0;
			}
			
			$notas = ['nota'=> $promedionota, 'asistencia'=> $promedioasistencia*10, 
					'estado'=> $estado, 'aprobada'=> $aprobada];
		}
		#if ($idmatricula == 74252) {
		#	echo var_dump($notas, '-', $idmatricula); exit;}
		return ($notas);
 
    }

	public function getNota($idmatricula)
    {
		$nota1 = 0;
		$nota2 = 0;
		$suma = [];
		$cont = [];
		$promedio = [];
		$ca = configuracion::find()->where(['dato'=> 'CA'])->one();
		$cb = configuracion::find()->where(['dato'=> 'CB'])->one();
		$cc = configuracion::find()->where(['dato'=> 'CC'])->one();
		$ex = configuracion::find()->where(['dato'=> 'EX'])->one();
		$as = configuracion::find()->where(['dato'=> 'AS'])->one();
		$ct = configuracion::find()->where(['dato'=> 'CT'])->one();
		$compA = $ca?$ca->valor/100:0;
		$compB = $cb?$cb->valor/100:0;
		$compC = $cc?$cc->valor/100:0;
		$compEx = $ex?$ex->valor/100:0;
		$compAs = $as?$as->valor/100:0;
		$compT = $ct?$ct->valor/100:0;

		$notas = NotasDetalle::find()
					
					->leftJoin('libreta_calificacion', 'libreta_calificacion.id = notas_detalle.idlibreta')
					->leftJoin('componentescalificacion', 'componentescalificacion.idcomponente = libreta_calificacion.idcomponente')
					->leftJoin('parametroscalificacion', 'parametroscalificacion.idparametro = componentescalificacion.idparametro')
					->where(['iddetallematricula'=>$idmatricula])
					->orderBy(['libreta_calificacion.hemisemestre'=>SORT_ASC, 'parametroscalificacion.idparametro'=>SORT_ASC]);

		$hemis = $notas->select(['libreta_calificacion.hemisemestre'])->groupBy(['libreta_calificacion.hemisemestre'])->all();
		$parametros = $notas->select(['parametroscalificacion.idparametro'])->groupBy(['parametroscalificacion.idparametro'])->all();

		foreach($notas->all() as $nota) {
			$cont = 0;
			foreach($hemis as $hemi) {
				if ($nota->hemisemestre == $hemi) {
					foreach($parametros as $parametro) {
						if ($nota->parametro == $parametro) {
							$suma[$hemi][$parametro] = isset($suma[$hemi][$parametro])?
								($suma[$hemi][$parametro] + $nota->nota):$nota->nota;
							$cont[$hemi][$parametro] = isset($cont[$hemi][$parametro])?($cont[$hemi][$parametro] + 1):1;
							break;
							break;
						}					
					}
				}
			}
		}
		
		echo var_dump($notas->all(), $suma, '-', $cont); exit;
		
		
		
		$aprobada = 0;
		if (count($row)) { 
			$nota1 = ( ($row["A1"]*$compA + $row["B1"]*$compB + $row["C1"]*$compC)*$compT + $row["Ex1"]*$compEx );
			$nota2 = ( ($row["A2"]*$compA + $row["B2"]*$compB + $row["C2"]*$compC)*$compT + $row["Ex2"]*$compEx );
			$asis1 = ($row["As1"] >=0 && $row["As1"] <=10)?$row["As1"]:$row["As1"];
			$asis2 = ($row["As2"] >=0 && $row["As2"] <=10)?$row["As2"]:$row["As2"];
			$promedionota = round((round($nota1) + round($nota2))/2, 2);
			$promedioasistencia = round((round($asis1) + round($asis2))/2, 2);
			$recp = round($row["Suf"]?$row["Suf"]:0);
			if ( $promedionota >= 7 && ($promedioasistencia >= 7 && $promedioasistencia <= 10) ) {
				$estado = 'APROBADA';
				$aprobada = 1;	
			}
			elseif ( $promedionota >= 5 && $promedionota < 7 && ($promedioasistencia >= 7 && $promedioasistencia <= 10) )
				if ($promedionota*2 + $recp >= 20){
					$estado = 'APROBADA';
					$promedionota = 7;
					$aprobada = 1;
				}
				else {
					$estado = 'REPROBADA';
					$aprobada = 0;
				}
			else {
				$estado = 'REPROBADA';
				$aprobada = 0;
			}
			
			$notas = ['nota'=> $promedionota, 'asistencia'=> $promedioasistencia*10, 
					'estado'=> $estado, 'aprobada'=> $aprobada];
		}
		#if ($idmatricula == 74252) {
		#	echo var_dump($notas, '-', $idmatricula); exit;}
		return ($notas);
 
    }

	//publiar notas estudiante
	public function actionPublicar($idcurso)
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

		$matriculas = DetalleMatricula::find()
				#->select('id')
				->where(['idcurso'=>$idcursomodel, 'estado'=> 1])
				->all();
		#$ids = explode(';', $idmatriculas);
		#$publicado = Docenteperasig::find()
		#							->where(['dpa_id'=> $iddpa])->one();
		#$start_time = microtime(true); 
		if ($matriculas && $cursomodel) {
			$nota = [];
			foreach($matriculas as $matricula) {
				
				$nota = $this->getNotas($matricula->id, $idcursomodel);
				
				$notasalumno = Notasalumnoasignatura::find()
										->where(['iddetalle'=> $matricula->id])
										->one();
				#$alumno = $matricula?$matricula->idFactura0->cedula:'';
				
				if ($notasalumno) {
					#$alumno = $notasalumno?$notasalumno->CIInfPer:'';
					#echo var_dump($matricula->id); exit;
					#$notasalumno->idPer = $idper;
					#$notasalumno->CIInfPer = $alumno;					
					$notasalumno->CalifFinal = $nota["nota"];
					$notasalumno->asistencia = $nota["asistencia"];
					$notasalumno->StatusCalif = 3;
					$notasalumno->observacion = $nota["estado"];
					$notasalumno->aprobada = $nota["aprobada"];
					$notasalumno->save();
					#if ($notasalumno->iddetalle == 74252) {
					#	echo var_dump($notasalumno->getErrors(), '-', $nota, '-', $notasalumno); exit;}
					//echo var_dump($notasalumno->getErrors(), $notasalumno->CalifFinal); exit;
				}
				else {
					#echo var_dump($matricula->id); exit;
					$alumno = $matricula?$matricula->idFactura0->cedula:'';
					$modelnota = new Notasalumnoasignatura();
					$modelnota->idPer = $idper;
					$modelnota->CIInfPer = $alumno;
					$modelnota->idAsig = $idasignatura;
					$modelnota->iddetalle = $matricula->id;
					$modelnota->CalifFinal = $nota["nota"];
					$modelnota->asistencia = $nota["asistencia"];
					$modelnota->StatusCalif = 3;
					$modelnota->observacion = $nota["estado"];
					$modelnota->aprobada = $nota["aprobada"];
					$modelnota->VRepite = 1;
					$modelnota->registro = date('Y-m-d H:i:s');
					$modelnota->convalidacion = 0;
						//$modelnota->observacion_efa = 'nota homologada';
					$modelnota->save();
						#echo var_dump($modelnota->errors); exit;
					
				}
				
			}
			#$end_time = microtime(true);			
			#$execution_time = ($end_time - $start_time)/60;			
			#echo var_dump($execution_time); exit;		
		}

		//**********************************************************************************************************
		
		$idsmatricula = ArrayHelper::getColumn($matriculas,'id');
		#echo var_dump($idsmatricula); exit;
		$query = new Query;
		// compose the query
		$query->select('notasalumnoasignatura.CIInfPer, ApellInfPer, ApellMatInfPer,NombInfPer,
				carrera.NombCarr, asignatura.NombAsig, periodolectivo.DescPerLec, 
				notasalumnoasignatura.CalifFinal, notasalumnoasignatura.observacion, 
				notasalumnoasignatura.asistencia, notasalumnoasignatura.idAsig, notasalumnoasignatura.aprobada')
			->from('notasalumnoasignatura')
			->join('INNER JOIN', 'detalle_matricula', 'detalle_matricula.id = notasalumnoasignatura.iddetalle')
			->join('INNER JOIN', 'carrera', 'carrera.idCarr = detalle_matricula.idcarr')
			->join('INNER JOIN', 'asignatura', 'asignatura.IdAsig = notasalumnoasignatura.IdAsig')
			->join('INNER JOIN', 'periodolectivo', 'periodolectivo.idper = notasalumnoasignatura.idper')
			->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = notasalumnoasignatura.CIInfPer')
			//->join('right JOIN', 'malla_curricular', 'malla_curricular.idAsig = notasalumnoasignatura.idAsig')
			->Where(['in', 'detalle_matricula.id', $idsmatricula ])
			->orderBy([
				'ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC,
				//'idmatriculas.idsemestre' => SORT_ASC,
				//'asignatura.NombAsig' => SORT_ASC,
			]);
			//->groupBy('notasalumnoasignatura.idAsig');


		// build and execute the query
		$rows = $query->all();

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
		#$pdf->write1DBarcode($idcursomodel, 'C39', '', '', '', 5, 0.2, '', 'N');
		$pdf->Cell(0, 0, $idcursomodel, 0, 1);
		

		// reset pointer to the last page
		$pdf->lastPage();
		$file = $iddocente . '_' . $idcursomodel . '.' . 'pdf';
		//Close and output PDF document
		$pdf->Output($file, 'D');
	
		//		$this->actionNotaspdf($matricula->idFactura0->cedula, $matricula->idcarr);

    }

	// get notas
	public function getQuerynotas($idper, $idcurso)
    {	
		$subquery = NotasDetalle::find()
					->select(['factura.cedula,componentescalificacion.idparametro, idlibreta, iddetallematricula, 
						hemisemestre, idfactura, libreta_calificacion.idcomponente, nota, 
						concat(ApellInfPer, " ", ApellMatInfPer, " ", NombInfPer) as estudiante,
						IF(hemisemestre = 1 && sigla = "A", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompA1,
						IF(hemisemestre = 1 && sigla = "B", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompB1,
						IF(hemisemestre = 1 && sigla = "C", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompC1,
						IF(hemisemestre = 1 && sigla = "X", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS EXM1,
						IF(hemisemestre = 1 && sigla = "T", (cast(sum(nota)*10/count(nota) as UNSIGNED)), "") AS Ast1,
						IF(hemisemestre = 2 && sigla = "A", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompA2,
						IF(hemisemestre = 2 && sigla = "B", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompB2,
						IF(hemisemestre = 2 && sigla = "C", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompC2,
						IF(hemisemestre = 2 && sigla = "X", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS EXM2,
						IF(hemisemestre = 2 && sigla = "T", (cast(sum(nota)*10/count(nota) as UNSIGNED)), "") AS Ast2,
						IF(hemisemestre = 0 && sigla = "R", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Rec
						'])
					->leftJoin('libreta_calificacion', 'libreta_calificacion.id = notas_detalle.idlibreta')
					->leftJoin('componentescalificacion', 'componentescalificacion.idcomponente = libreta_calificacion.idcomponente')
					->leftJoin('parametroscalificacion', 'parametroscalificacion.idparametro = componentescalificacion.idparametro')
					->leftJoin('detalle_matricula', 'detalle_matricula.id = notas_detalle.iddetallematricula')
					->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
					->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
					->where(['libreta_calificacion.idper'=>$idper, 'libreta_calificacion.idcurso' => $idcurso,
							'detalle_matricula.estado'=> 1])
					->groupBy(['factura.cedula', 'libreta_calificacion.hemisemestre','componentescalificacion.idparametro'])
					->orderBy(['ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC, 
								'libreta_calificacion.hemisemestre'=>SORT_ASC, 'componentescalificacion.idparametro'=>SORT_ASC]);
	

		$query = new Query();
		$query->select(['*'])
			->addSelect(['sum(c.CompA1) as A1, sum(c.CompB1) as B1, sum(c.CompC1) as C1, sum(c.EXM1) as Ex1, sum(c.Ast1) as As1,
						sum(c.CompA2) as A2, sum(c.CompB2) as B2, sum(c.CompC2) as C2, sum(c.EXM2) as Ex2, sum(c.Ast2) as As2,
						sum(c.Rec) as Suf
						'])
			->from(['c' => $subquery])
			->groupBy(['cedula'])
			->orderBy(['estudiante'=>SORT_ASC]);
		
		return $query;
	}

	public function getHistoriconotas($idper, $iddocente, $idcarr, $idasig, $nivel, $paralelo)
    {	
		$subquery = NotasDetalle::find()
					->select(['factura.cedula,componentescalificacion.idparametro, idlibreta, iddetallematricula, 
						hemisemestre, idfactura, libreta_calificacion.idcomponente, nota, 
						concat(ApellInfPer, " ", ApellMatInfPer, " ", NombInfPer) as estudiante,
						IF(hemisemestre = 1 && sigla = "A", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompA1,
						IF(hemisemestre = 1 && sigla = "B", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompB1,
						IF(hemisemestre = 1 && sigla = "C", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompC1,
						IF(hemisemestre = 1 && sigla = "X", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS EXM1,
						IF(hemisemestre = 1 && sigla = "T", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Ast1,
						IF(hemisemestre = 2 && sigla = "A", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompA2,
						IF(hemisemestre = 2 && sigla = "B", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompB2,
						IF(hemisemestre = 2 && sigla = "C", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS CompC2,
						IF(hemisemestre = 2 && sigla = "X", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS EXM2,
						IF(hemisemestre = 2 && sigla = "T", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Ast2,
						IF(hemisemestre = 0 && sigla = "R", (cast(sum(nota)/count(nota) as UNSIGNED)), "") AS Rec
						'])
					->leftJoin('libreta_calificacion', 'libreta_calificacion.id = notas_detalle.idlibreta')
					->leftJoin('componentescalificacion', 'componentescalificacion.idcomponente = libreta_calificacion.idcomponente')
					->leftJoin('parametroscalificacion', 'parametroscalificacion.idparametro = componentescalificacion.idparametro')
					->leftJoin('detalle_matricula', 'detalle_matricula.id = notas_detalle.iddetallematricula')
					->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
					->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
					->where(['libreta_calificacion.idper'=>$idper, 'libreta_calificacion.iddocente'=>$iddocente,
								'detalle_matricula.idcarr' => $idcarr,
								'detalle_matricula.idasig'=>$idasig, 'detalle_matricula.nivel' => $nivel,
								'detalle_matricula.paralelo'=>$paralelo,
							])
					->groupBy(['factura.cedula', 'libreta_calificacion.hemisemestre','componentescalificacion.idparametro'])
					->orderBy(['ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC, 
								'libreta_calificacion.hemisemestre'=>SORT_ASC, 'componentescalificacion.idparametro'=>SORT_ASC]);
	

		$query = new Query();
		$query->select(['*'])
			->addSelect(['sum(c.CompA1) as A1, sum(c.CompB1) as B1, sum(c.CompC1) as C1, sum(c.EXM1) as Ex1, sum(c.Ast1) as As1,
						sum(c.CompA2) as A2, sum(c.CompB2) as B2, sum(c.CompC2) as C2, sum(c.EXM2) as Ex2, sum(c.Ast2) as As2,
						sum(c.Rec) as Suf
						'])
			->from(['c' => $subquery])
			->groupBy(['cedula'])
			->orderBy(['estudiante'=>SORT_ASC]);
		
		return $query;
	}

	public function getAsistencia($idcurso)
    {	

		$subquerynota = NotasDetalle::find()
					->select(['libreta_calificacion.fecha as fecha, factura.cedula as cedula, 
								concat(ApellInfPer, " ", ApellMatInfPer, " ", NombInfPer) as estudiante, nota'])
					->leftJoin('libreta_calificacion', 'libreta_calificacion.id = notas_detalle.idlibreta')
					->leftJoin('componentescalificacion', 'componentescalificacion.idcomponente = libreta_calificacion.idcomponente')
					->leftJoin('detalle_matricula', 'detalle_matricula.id = notas_detalle.iddetallematricula')
					->leftJoin('factura', 'factura.id = detalle_matricula.idfactura')
					->leftJoin('informacionpersonal', 'informacionpersonal.CIInfPer = factura.cedula')
					->where(['libreta_calificacion.idcurso' => 3955, 'componentescalificacion.componente'=> 'Asistencia'])
					//->groupBy(['libreta_calificacion.fecha']);
					->orderBy(['libreta_calificacion.hemisemestre'=>SORT_ASC, 'libreta_calificacion.fecha'=>SORT_ASC,
								'ApellInfPer'=>SORT_ASC, 'ApellMatInfPer'=>SORT_ASC, 'NombInfPer'=>SORT_ASC]);
		$subquery = new Query();
		$cedula = '';
		$estudiante = array();
		$cont = 0;
		foreach ($subquerynota->each() as $notac) {
			if ($cedula != $notac->cedula) {
				//array_push($dato,$notac->cedula); 
				$dato = [];
				$cedula = $notac->cedula;
				$querya = new Query();
				$querya->select(['*'])
					//->addSelect(['sum(c.Ast1) as As1,sum(c.Ast2) as As2'])
					->from(['c' => $subquerynota])
					->where(['cedula'=>$cedula]);
				$cont = 0;
				foreach ($querya->each() as $fecha) {
					//print_r($fecha);exit;
					//array_push($dato,$fecha['fecha']); 
					$newDate = date("dmY", strtotime($fecha['fecha']));
					$dato[$newDate] = round($fecha['nota']);
					$cont++;
				}
			}
						
			$estudiante[$cedula] = $dato;
			//$$estudiante['fecha'] = $dato;
			
			// ...
		}
		//$subquery = $estudiante;
		//$query = new Query();
		//$query->select(['*'])
			//->addSelect(['sum(c.Ast1) as As1,sum(c.Ast2) as As2'])
			///->from(['c' => $estudiante]);
			//->groupBy('fecha')
			//->orderBy(['estudiante'=>SORT_ASC]);
		//echo var_dump($estudiante); exit;

		return $estudiante;
	}

	public function set_parametros()
    {
		$ca = configuracion::find()->where(['dato'=> 'CA'])->one();
		$cb = configuracion::find()->where(['dato'=> 'CB'])->one();
		$cc = configuracion::find()->where(['dato'=> 'CC'])->one();
		$ex = configuracion::find()->where(['dato'=> 'EX'])->one();
		$as = configuracion::find()->where(['dato'=> 'AS'])->one();
		$ct = configuracion::find()->where(['dato'=> 'CT'])->one();
		$compA = $ca?$ca->valor/100:0;
		$compB = $cb?$cb->valor/100:0;
		$compC = $cc?$cc->valor/100:0;
		$compEx = $ex?$ex->valor/100:0;
		$compAs = $as?$as->valor/100:0;
		$compT = $ct?$ct->valor/100:0;
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
		
		//echo var_dump($row['NombCarr']);
		//exit;
		$observacion = '';
		
		if ($row['aprobada'] == 1)
			$observacion = 'APROBADA';
		else if  ($row['aprobada'] == 0)
			$observacion = 'REPROBADA';
		$pdf->Cell($w[0], 6, $row['CIInfPer'], 'LR', 0, 'L', $fill);
		//$pdf->Cell($w[1], 6, number_format($row['idsemestre']), 'LR', 0, 'C', $fill);
		$pdf->Cell($w[1], 6, $row['ApellInfPer'].' '.$row['ApellMatInfPer'].' '.$row['NombInfPer'], 'LR', 0, 'L', $fill);
		$pdf->Cell($w[2], 6, $row['CalifFinal'], 'LR', 0, 'C', $fill);
		$pdf->Cell($w[3], 6, $row['asistencia'].'%', 'LR', 0, 'C', $fill);
		$pdf->Cell($w[4], 6, $row['observacion'], 'LR', 0, 'C', $fill);
		//$pdf->Cell($w[6], 6, $observacion, 'LR', 0, 'L', $fill);
		$pdf->Ln();
		$fill=!$fill;
        }
	
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }


    /**
     * Finds the NotasDetalle model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NotasDetalle the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NotasDetalle::findOne($id)) !== null) {
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


