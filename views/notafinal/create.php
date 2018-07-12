<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\NotaFinal */

$this->title = 'Create Nota Final';
$this->params['breadcrumbs'][] = ['label' => 'Nota Finals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-final-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
