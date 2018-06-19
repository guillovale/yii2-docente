<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\NotasDetalle */
/* @var $form yii\widgets\ActiveForm */
$componente =  $this->params['componente'];
$parametro =  $this->params['parametro'];
$hemi =  $this->params['hemisemestre'];

?>
<div class="col-xs-4">
	<h3><?= Html::encode($this->title) ?></h3>
	<address>
		<b>Asignatura: <?= $this->params['asignatura'] ?></b><br>
		Nivel: <?= $this->params['nivel'] ?>
		Paralelo: <?= $this->params['paralelo'] ?>
	</address>

    <?php $form = ActiveForm::begin(); ?>
	
	<fieldset>
        	<legend></legend>
		
		<?= $form->field($libretaModel, 'hemisemestre')->dropDownList($hemi, ['prompt'=>'Elija...',
						
							'onchange'=>'
		                $.get( "'.Url::toRoute('/notasdetalle/listarcomponente').'", 
							{ id: $(this).val() + ";" + "'.$libretaModel->curso->detallemalla->malla->detalle.'" } )
		                    .done(function( data ) {
								//alert ("'.$libretaModel->curso->detallemalla->malla->detalle.'");
		                        $( "#'.Html::getInputId($libretaModel, 'idparametro').'" ).html( data );
								$( "#'.Html::getInputId($libretaModel, 'idcomponente').'" ).html( "<option>-</option>" );
		                    }
		                );
		            '
		]) ?>

		<?= $form->field($libretaModel, 'idparametro')->dropDownList(array(), ['prompt'=>'Elija...',
						
							'onchange'=>'
		                $.get( "'.Url::toRoute('/notasdetalle/listar').'", { id: $(this).val() } )
		                    .done(function( data ) {
		                        $( "#'.Html::getInputId($libretaModel, 'idcomponente').'" ).html( data );
		                    }
		                );
		            '

						]) ?>

	

		<?= $form->field($libretaModel, 'idcomponente')->dropDownList(['prompt'=>'Elija...']) ?>
	
	  	
		<?= $form->field($libretaModel, 'tema'); ?>

	</fieldset>
</div>

<div class="col-xs-8">
	<table class="table table-striped">
	<thead>
        <tr><th>No.</th><th>Cédula</th><th>Alumno</th><th>Nota</th></tr>
	</thead>
	<tbody>
        <?php foreach($settings as $i=>$item): ?>
	
            <tr>
				<td><?= $i+1; ?></td>
				<td><?=$settings[$i]["cedula0"]; ?></td>
				<td><?=$settings[$i]["cedula"]; ?></td>
		        <td><?= $form->field($item,"[$i]nota")->textInput()->label(false); ?></td>
            </tr>
        <?php endforeach; ?>
	</tbody>
    </table>
    
    <div class="form-group">
		<?php if ($settings) {
        echo Html::submitButton('Crear Nota', ['class' => 'btn btn-success']);}
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
