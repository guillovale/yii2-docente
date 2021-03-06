<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CursoOfertadoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista asignaturas:';
$this->params['breadcrumbs'][] = $this->title;
$cedula =  $this->params['cedula'];
$docente =  $this->params['docente'];
?>
<div class="curso-ofertado-index">

    <h3><?= Html::encode($this->title) ?></h3>
	<address>
		C.I.: <?= $this->params['cedula'] ?> <br>
		<b>Docente: <?= $this->params['docente'] ?> </b><br>
	</address>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Create Curso Ofertado', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
			//'idper',
			'periodo.DescPerLec',
            //'iddetallemalla',
			//'detallemalla.malla.idcarrera',
			'detallemalla.malla.carrera.NombCarr',
			'detallemalla.asignatura.NombAsig',
            //'iddocente',
			'detallemalla.nivel',
			[
				'attribute'=>'paralelo',
				'format'=>'text',//raw, html
				'filter'=>false,
				'enableSorting' => false,
			],
			[
				'attribute'=>'fecha_inicio',
				'format'=>'text',//raw, html
				'filter'=>false,
				'enableSorting' => false,
			],
			[
				'attribute'=>'fecha_fin',
				'format'=>'text',//raw, html
				'filter'=>false,
				'enableSorting' => false,
			],
			//'paralelo',
            // 'cupo',
            // 'idhorario',
            // 'estado',
            // 'restringido',

            ['class' => 'yii\grid\ActionColumn',
				'template' => '{imprimir}',
				'buttons' => [
					'imprimir' => function ($url) {
						return Html::a('<span class="glyphicon glyphicon-print"></span>',$url);
					},
				],
				'urlCreator' => function ($action, $model, $key, $index) {
					
					if ($action === 'imprimir') {
						        //$url ='/notasalumnoasignatura/delete?id='.$model->idnaa;
						if ($model->detallemalla->nivel > 0 )	{
							$url = Url::to(['notasdetalle/index', 'idcurso'=> $model->id], true);
							
						}
						else	{
							$url = Url::to(['libretacalificacion/consolidado', 'idcurso'=> $model->id], true);
						}				
						return $url;
							
						}
						
				},
			],
        ],
    ]); ?>
</div>
