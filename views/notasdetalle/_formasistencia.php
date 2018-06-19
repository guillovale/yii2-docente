<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\NotasDetalle */
/* @var $form yii\widgets\ActiveForm */
//$componente =  $this->params['componente'];
//$parametro =  $this->params['parametro'];
$hoy = date("F j, Y, g:i a");
?>
<div class="col-xs-4">
	<?= $hoy ?>
	<h3><?= Html::encode($this->title) ?></h3>
	<address>
		<b>Asignatura: <?= $this->params['asignatura'] ?></b><br>
		Nivel: <?= $this->params['nivel'] ?>
		Paralelo: <?= $this->params['paralelo'] ?>
	</address>

    <?php $form = ActiveForm::begin(); ?>
	
	<fieldset>
        	<legend></legend>
		Hemisemestre: <?= $libretaModel->hemisemestre; ?><br>
		
		<?= $form->field($libretaModel, 'tema')->label('observación'); ?>

	</fieldset>
</div>

<div class="col-xs-8">
	<table class="table table-striped">
	<thead>
        <tr><th>No.</th><th>Cédula</th><th>Alumno</th><th>Asistencia</th></tr>
	</thead>
	<tbody>
        <?php foreach($settings as $i=>$item): ?>
	
            <tr>
				<td><?= $i+1; ?></td>
				<td><?=$settings[$i]["cedula0"]; ?></td>
				<td><?=$settings[$i]["cedula"]; ?></td>
		
                <td><?= $form->field($item,"[$i]nota")->checkbox(['label' => '', 
													'uncheck' => 0, 'checked' => 1]); ?></td>
            </tr>
        <?php endforeach; ?>
	</tbody>
    </table>
    
    <div class="form-group">
		<?php if ($settings) {
        echo Html::submitButton('Crear asistencia', ['class' => 'btn btn-success']);}
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
