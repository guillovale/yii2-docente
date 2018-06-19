<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\DocenteperasigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista asignaturas: ';
$this->params['breadcrumbs'][] = $this->title;
$cedula =  $this->params['cedula'];
$docente =  $this->params['docente'];
?>
<div class="docenteperasig-index">

    <h4><?= Html::encode($this->title) ?></h4>
	<address>
		C.I.: <?= $this->params['cedula'] ?> <br>
		<b>Docente: <?= $this->params['docente'] ?> </b><br>
	</address>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Docenteperasig', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'dpa_id',
           // 'CIInfPer',
		//'idPer',
		'idPer0.DescPerLec',
		[
			'attribute'=>'idCarr',
			'format'=>'text',//raw, html
			'filter'=>false,
			'enableSorting' => false,
	    ],
	[
			'attribute'=>'Carrera',
			'format'=>'text',//raw, html
			'filter'=>false,
			'enableSorting' => false,
	    ],
	[
			'attribute'=>'idAsig',
			'format'=>'text',//raw, html
			'filter'=>false,
			'enableSorting' => false,
	],
	[
			'attribute'=>'asignatura',
			'format'=>'text',//raw, html
			'filter'=>false,
			'enableSorting' => false,
	],

	[
			'attribute'=>'idSemestre',
			'format'=>'text',//raw, html
			'filter'=>false,
			'enableSorting' => false,
	],

	[
			'attribute'=>'idParalelo',
			'format'=>'text',//raw, html
			'filter'=>false,
			'enableSorting' => false,
	],
           
           // 'idAsig',
            //'idCarr',
            // 'idAnio',
            // 'idSemestre',
            // 'idParalelo',
            // 'status',
            // 'idMc',
            // 'tipo_orgmalla',
            // 'id_actdist',
            // 'id_contdoc',
            // 'transf_asistencia',
            // 'transf_frecuente',
            // 'transf_parcial',
            // 'transf_final',
            // 'arrastre',
            // 'extra',

            ['class' => 'yii\grid\ActionColumn',
				'template' => '{view}{crear}',
				
				'template' => '{view}',
				'buttons' => [
					'view' => function ($url) {
						return Html::a('<span class="glyphicon glyphicon-print"></span>',$url);
					},
				],
	
				'urlCreator' => function ($action, $model, $key, $index) {
					if ($action === 'crear') {
						        //$url ='/notasalumnoasignatura/delete?id='.$model->idnaa;
						/*
						$url = Url::to(['notas/crear', 'id'=>$model->id, 'idfactura'=>$model->idfactura
									, 'cedula'=>$this->params['cedula'], 'total'=>$this->params['total']], true);
						        return $url;
						*/
						}
						

					if ($action === 'view') {
						        //$url ='/notasalumnoasignatura/delete?id='.$model->idnaa;
						
						$url = Url::to(['notasdetalle/index', 
								'idcarr'=>$model->idCarr, 
								'idasig'=>$model->idAsig,
								'nivel'=>$model->idSemestre, 
								'paralelo'=>$model->idParalelo, 
								'idper'=> $model->idPer, 
								'iddocente'=> $model->CIInfPer,
								'idcurso'=>0,
								], true);
						        return $url;
							
						}
						
				},

				],
			
        ],
    ]); ?>
</div>
