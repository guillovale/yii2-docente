<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CursoOfertadoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="curso-ofertado-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'idper') ?>

    <?= $form->field($model, 'iddetallemalla') ?>

    <?= $form->field($model, 'iddocente') ?>

    <?= $form->field($model, 'paralelo') ?>

    <?php // echo $form->field($model, 'cupo') ?>

    <?php // echo $form->field($model, 'idhorario') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'restringido') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
