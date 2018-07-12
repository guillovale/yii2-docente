<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LibretaCalificacionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$periodo =  $this->params['periodo'];
$carrera =  $this->params['carrera'];
$asignatura =  $this->params['asignatura'];
$paralelo =  $this->params['paralelo'];
$nivel =  $this->params['nivel'];
$idper = $this->params['idper'];
$idcarr = $this->params['idcarr'];
$idasig = $this->params['idasig'];
$idcurso = $this->params['idcurso'];
$silabo = $this->params['silabo'];
$this->title = 'Libreta Calificaciones';
$this->params['breadcrumbs'][] = ['label' => 'Asignatura', 'url' => ['cursoofertado/index']];
$this->params['breadcrumbs'][] = $this->title;
$pdf = 'uploads/'.$idcurso.'.'.'pdf';
		
?>
<div class="libreta-calificacion-index">

    <h3><?= Html::encode($this->title) ?></h3>
	<address>
		Período: <?= $this->params['periodo'] ?> <br>
		Carrera: <?= $this->params['carrera'] ?> <br>
		<b>Asignatura: <?= $this->params['asignatura'] ?> </b><br>
		Nivel: <?= $this->params['nivel'] ?> 
		Paralelo: <?= $this->params['paralelo'] ?> </b><br>
		Sílabo: <?= Html::a($silabo, [$pdf]); ?>
	</address>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <? Html::a('Crear Calificación', ['notasdetalle/create', 'idcarr'=>$idcarr, 'idasig'=>$idasig,
								'nivel'=>$nivel, 
								'paralelo'=>$paralelo, 
								'idper'=> $idper, 'idcurso'=> $idcurso], 
											['class' => 'btn btn-primary']) ?>
												
		<?= Html::a('Crear Calificación', ['notasdetalle/creamasivo', 'idcurso'=> $idcurso], 
											['class' => 'btn btn-primary']) ?>
											
	<?php if ($nivel == 0) {
		echo Html::a('Ver Calificaciones', ['consolidado', 'idcurso'=> $idcurso], 
											['class' => 'btn btn-warning']);} 
		else {
			echo Html::a('Ver Calificaciones', ['notasdetalle/index', 'idcurso'=> $idcurso], 
											['class' => 'btn btn-warning']);
		}
	?>

	<?php  echo Html::a('Agregar Alumnos a componentes', ['agregar', 'idcurso'=>$idcurso],
								 ['class'=>'btn btn-success']); ?>

    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'idper',
		//'iddocenteperasig',
            //'iddocente',
	    'fecha',
            'hemisemestre',
            //'idparametro',
		'componente0.idParam.parametro',
		'componente',
          //  'idcomponente',
            'tema',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{delete}'],
        ],
    ]); ?>
</div>
