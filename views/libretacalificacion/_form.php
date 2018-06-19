<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\LibretaCalificacion */
/* @var $form yii\widgets\ActiveForm */
$componente =  $this->params['componente'];
$parametro =  $this->params['parametro'];
$habilitar =  $this->params['habilitar'];
?>

<div class="libreta-calificacion-form">

    <?php $form = ActiveForm::begin(); ?>
	<?= $form->field($model, 'hemisemestre')->textInput(['readonly'=> true]) ?>
    
    <?= $form->field($model, 'idparametro')->dropDownList($parametro, ['disabled' => $habilitar, 'prompt'=>'Elija...',
						
							'onchange'=>'
		                $.get( "'.Url::toRoute('/libretacalificacion/listar').'", { id: $(this).val() } )
		                    .done(function( data ) {
		                        $( "#'.Html::getInputId($model, 'idcomponente').'" ).html( data );
		                    }
		                );
		            '

						]) ?>

    <?= $form->field($model, 'idcomponente')->dropDownList($componente,['disabled' => $habilitar,'prompt'=>'Elija...']) ?>

    <?= $form->field($model, 'tema')->textInput(['readonly'=> $habilitar]) ?>

    <div class="form-group">
		<?php if(!$habilitar){
       		echo Html::submitButton('Grabar', ['class' => 'btn btn-success']);} 
		?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
