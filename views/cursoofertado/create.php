<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CursoOfertado */

$this->title = 'Create Curso Ofertado';
$this->params['breadcrumbs'][] = ['label' => 'Curso Ofertados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="curso-ofertado-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
