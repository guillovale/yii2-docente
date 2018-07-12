<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\NotaFinal */

$this->title = 'Update Nota Final: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Finals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nota-final-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
