<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\NotasDetalleSearch */
/* @var $form yii\widgets\ActiveForm */
$componente =  $this->params['componente'];
$parametro =  $this->params['parametro'];
?>

<div class="notas-detalle-search">

    <?php $form = ActiveForm::begin(); ?>

	<?= $form->field($libretaModel, 'hemisemestre')->radioList([1 => 'Primero', 2 => 'Segundo']) ?>
	<?= $form->field($libretaModel, 'idparametro')->dropDownList($parametro, ['prompt'=>'Elija...',
						
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

	<div class="form-group">
        <?= Html::submitButton($libretaModel->isNewRecord ? 'Create' : 'Update', ['class' => $libretaModel->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
