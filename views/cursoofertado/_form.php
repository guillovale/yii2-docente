<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CursoOfertado */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="curso-ofertado-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idper')->textInput() ?>

    <?= $form->field($model, 'iddetallemalla')->textInput() ?>

    <?= $form->field($model, 'iddocente')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paralelo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cupo')->textInput() ?>

    <?= $form->field($model, 'idhorario')->textInput() ?>

    <?= $form->field($model, 'estado')->textInput() ?>

    <?= $form->field($model, 'restringido')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
