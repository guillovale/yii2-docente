<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\NotaFinalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nota-final-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idmatricula') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'A1') ?>

    <?= $form->field($model, 'A2') ?>

    <?php // echo $form->field($model, 'B1') ?>

    <?php // echo $form->field($model, 'B2') ?>

    <?php // echo $form->field($model, 'C1') ?>

    <?php // echo $form->field($model, 'C2') ?>

    <?php // echo $form->field($model, 'AS1') ?>

    <?php // echo $form->field($model, 'AS2') ?>

    <?php // echo $form->field($model, 'X1') ?>

    <?php // echo $form->field($model, 'X2') ?>

    <?php // echo $form->field($model, 'RC') ?>

    <?php // echo $form->field($model, 'CF') ?>

    <?php // echo $form->field($model, 'ASF') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'tipo_estado') ?>

    <?php // echo $form->field($model, 'observacion') ?>

    <?php // echo $form->field($model, 'usuario_modifica') ?>

    <?php // echo $form->field($model, 'fecha_modifica') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
