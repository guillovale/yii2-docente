<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NotasDetalle */
$alumno = $this->params['alumno'];
$this->title = 'Actualizar Nota:'; // . $model->idnota;
$this->params['breadcrumbs'][] = ['label' => 'Detalle Notas', 'url' => ['libretacalificacion/view', 'id' => $model->idlibreta]];
//$this->params['breadcrumbs'][] = ['label' => $model->idnota, 'url' => ['libretacalificacion/view', 'id' => $model->idlibreta]];
$this->params['breadcrumbs'][] = 'Actualizar';

?>
<div class="notas-detalle-update">

    <h3><?= Html::encode($this->title) ?></h3>
	<address>
		Alumno: <?= $alumno ?> <br>
		<b>Asignatura: <?= $this->params['idasig'] ?> </b><br>
		
	</address>

    <?= $this->render('_formnota', [
        'model' => $model,
    ]) ?>

</div>
