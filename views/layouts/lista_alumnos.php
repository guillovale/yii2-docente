<?php $this->beginContent('@app/views/layouts/main.php'); ?>
        
<div class="row">

<div class="col-md-8">
	<div style="font-size:14px;">
		<?= $content; ?>
	</div>
</div>


<div class="col-md-3">
	<?php
		use yii\grid\GridView;
			#use yii\widgets\DetailView;
			use yii\data\ActiveDataProvider;
			use yii\helpers\Html;
	?>
	<div style="font-size:11px;">
		<h4 style="color:blue;">Horario</h4>
		
		<?php
		
			
					
			$queryh = $this->params['horarios'];
			$dataProviderh = new ActiveDataProvider([
				'query' => $queryh,
				'pagination' => [
					'pageSize' => 20,
				],
				'sort' =>false,
			]);

			$model = $dataProviderh->getModels();
			echo GridView::widget([
			    'dataProvider' => $dataProviderh,
				'columns' => [
					['class' => 'yii\grid\SerialColumn'],
				'dia',                                         
				'hora_inicio',                                
				'hora_fin',
			    ],
			]);
		
		?>
		<h5 style="color:blue;">Lista Alumnos</h5>
			
		<?php
			#use yii\grid\GridView;
			#use yii\widgets\DetailView;
			#use yii\data\ActiveDataProvider;
			#use yii\helpers\Html;
			
			$query = $this->params['totalalumnos'];
			#$queryh = $this->params['horarios'];

			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => [
					'pageSize' => 100,
				],
				'sort' =>false,
			]);

					
			
			echo GridView::widget([
				'dataProvider' => $dataProvider,
				'columns' => [
					['class' => 'yii\grid\SerialColumn'],
					'idFactura0.cedula',
					[
						
						'label'=>'Alumno',
						'format'=>'text',//raw, html
						#'contentOptions' => ['style' => 'color:blue;'],
						'content'=>function($data) {
							return $data->idFactura0->getAlumno();
						}
					],
						//'cedula',
						//'alumno',
						//'NombreCarrera',
						//'matricula->idParalelo',
						//'cnt',
					#'id',
					
						//'nota',
					],
			]);		
			

			
		
		?>


	</div>
   
  </div>
  
</div>

	
<?php $this->endContent();
