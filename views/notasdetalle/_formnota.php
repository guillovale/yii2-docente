<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LibretaCalificacion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="libreta-calificacion-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idlibreta')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'nota')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Actualizar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
