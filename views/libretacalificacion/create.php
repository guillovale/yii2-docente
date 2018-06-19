<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LibretaCalificacion */

$this->title = 'Create Libreta Calificacion';
$this->params['breadcrumbs'][] = ['label' => 'Libreta Calificacions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="libreta-calificacion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
