<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\LibretaCalificacion */
$asignatura = $this->params['idasig'];
$this->title = 'Registro No. '. $model->id;
//$this->params['breadcrumbs'][] = ['label' => 'Libreta Calificacions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Libreta', 'url' => Url::previous()];
$this->params['breadcrumbs'][] = $this->title;
$modelNotas = $dataProvider->getModels();
$habilitar =  $this->params['habilitar'];

?>
<div class="row">

    <h4><?= Html::encode($this->title) ?></h4>
	<div class="col-xs-4">

		<?= $this->render('_form', [
		    'model' => $model,
		]) ?>

	</div>
	
	<div class="col-xs-8">
		<?= $asignatura ?>
	<p>
		<?php if(!$habilitar){
       		echo Html::a('Agregar alumno a lista', ['libretacalificacion/agregar', 'idlibreta'=>$idlibreta]);} 
		?>
        

   	</p>
	 <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $modelNotas,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

		//'idlibreta',
		'cedula0',
            'alumno',
            'nota',
			'peso',
		//	'iddetallematricula',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{update}',

		'urlCreator' => function ($action, $model, $key, $index) {
										

					if ($action === 'update') {
						        //$url ='/notasalumnoasignatura/delete?id='.$model->idnaa;
						
						$url = Url::to(['notasdetalle/update', 'id'=>$model->idnota], true);
						        return $url;
							
						}
						
				},		

	],
        ],
    ]); ?>
	</div>
</div>
