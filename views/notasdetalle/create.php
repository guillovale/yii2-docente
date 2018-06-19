<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\NotasDetalle */

$this->title = 'Crear Notas';
//$this->params['breadcrumbs'][] = ['label' => 'Notas Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Libreta', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	
    <?= $this->render('_form', [
        'settings' => $settings, 'libretaModel'=> $libretaModel
    ]) ?>

</div>
