<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resumen de notas estudiantes:';
$this->params['breadcrumbs'][] = $this->title;
$periodo =  $this->params['periodo'];
$carrera =  $this->params['carrera'];
$asignatura =  $this->params['asignatura'];
$paralelo =  $this->params['paralelo'];
$nivel =  $this->params['nivel'];
$idper = $this->params['idper'];
$idcarr = $this->params['idcarr'];
$idasig = $this->params['idasig'];

?>
<div class="detalle-matricula-index">

    <h3><?= Html::encode($this->title) ?></h3>
	<address>
		Período: <?= $this->params['periodo'] ?> <br>
		Carrera: <?= $this->params['carrera'] ?> <br>
		<b>Asignatura: <?= $this->params['asignatura'] ?> </b><br>
		Nivel: <?= $this->params['nivel'] ?> 
		Paralelo: <?= $this->params['paralelo'] ?> </b>
	</address>
	

   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'idfactura',
            //'idcarr',
            //'idmatricula',
            //'idasig',
            //'nivel',
            //'paralelo',
            // 'idnota',
            // 'credito',
            // 'vrepite',
            // 'costo',
            // 'horario',
            //'fecha',
            //'estado',
			
			'alumno',
			[
			//'attribute'=>'idMatricula',
			'label'=>'C.D.P.1',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'C.P.A.1',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'C.A.A.1',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'Exm.1',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'C.D.P.2',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'C.P.A.2',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'C.A.A.2',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'Exm.2',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'Sup.',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'Final',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
			//'attribute'=>'idMatricula',
			'label'=>'Asist.',
			'format'=>'text',//raw, html
			'content'=>function($data){
				return 0.0;#$data->getAsignatura();
	                }
	        ],
			[
				'label'=>'Estado',
				'format'=>'text',//raw, html
				'contentOptions'=> function($data){	
					if ($data->estado == 1){
						return ['style'=>'color: black;']; // <-- right here
					}
					else
						return ['style'=>'color: red;'];
					},
	        ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
