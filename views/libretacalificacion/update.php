<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LibretaCalificacion */

$this->title = 'Update Libreta Calificacion: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Libreta Calificacions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="libreta-calificacion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
