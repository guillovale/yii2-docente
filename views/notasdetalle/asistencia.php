<!-- CSS media query within a stylesheet -->
<style>
@media print {
  @page {
  size: auto;
  margin: 0;
       }
.footer, .header { display:none;} 
  #printPageButton {
    display: none;
  }
  a[href] {
    display: none;
  }
 
}
</style>
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotasDetalleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resumen de notas:';
//$this->params['breadcrumbs'][] = ['label' => 'Libreta', 'url' => Yii::$app->request->referrer];
//$this->params['breadcrumbs'][] = $this->title;
$periodo =  $this->params['periodo'];
$carrera =  $this->params['carrera'];
$docente = $this->params['docente'];
$iddocente = $this->params['cedula'];
$asignatura =  $this->params['asignatura'];
$paralelo =  $this->params['paralelo'];
$nivel =  $this->params['nivel'];
$idper = $this->params['idper'];
$idcarr = $this->params['idcarr'];
$idasig = $this->params['idasig'];
$idcurso = $this->params['idcurso'];
$publicar = $this->params['publicar'];


?>
<div style = "font-size:11px" class="row">
	<div class="row">
		<?= Html::img('@web/uploads/encabezadoa.jpg', ['alt'=>'some', 'class'=>'thing']);?>
		
		<?= Html::a( 'Regresar', Yii::$app->request->referrer, ['class' => 'btn btn-warning btn-ms']);	?>
		<button id="printPageButton", onclick="window.print()">Imprimir</button>
	</div>

	<div class="notas-detalle-index">
		
		<h4>  Asignatura: <?= $this->params['asignatura'] ?> </h4>
		<address>
			Fecha: <?= date("Y-m-d"); ?> <br>
			Docente: <?= $this->params['docente'] ?> <br>
			Período: <?= $this->params['periodo'] ?> <br>
			Carrera: <?= $this->params['carrera'] ?> <br>
			Nivel: <?= $this->params['nivel'] ?> 
			Paralelo: <?= $this->params['paralelo'] ?> </b>
		</address>
		<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

		<?php if (count($query) > 0): ?>
			<table>
			  <thead>
				<tr>
				<th>Alumno</th>
				  <th><?php echo implode('</th><th>', array_keys(current($query))); ?></th>
				</tr>
			  </thead>
			  <tbody>
			<?php foreach ($query as $row): array_map('htmlentities', $row); ?>
				
				<tr>
					
				  <td><?php echo implode('</td><td>', $row); ?></td>
				</tr>
			<?php endforeach; ?>
			  </tbody>
			</table>
		<?php endif; ?>
	</div>

<footer>
  <address style="font-size:90%;">
	</br>
	</br>
	</br>
	F.)-------------------------------------
	</br>
	Docente
	</br></br>
	Entregar firmado en la Secretaría Técnica Académica de su Facultad.

  </address>
</footer>
</div>
