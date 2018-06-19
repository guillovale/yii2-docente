<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\NotasDetalle */

$this->title = 'Asistencia';
//$this->params['breadcrumbs'][] = ['label' => 'Notas Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Libreta', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	
    <?= $this->render('_formasistencia', [
        'settings' => $settings, 'libretaModel'=> $libretaModel
    ]) ?>

</div>
