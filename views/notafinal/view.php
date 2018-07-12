<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\NotaFinal */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Nota Finals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-final-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idmatricula',
            'fecha',
            'A1',
            'A2',
            'B1',
            'B2',
            'C1',
            'C2',
            'AS1',
            'AS2',
            'X1',
            'X2',
            'RC',
            'CF',
            'ASF',
            'estado',
            'tipo_estado',
            'observacion',
            'usuario_modifica',
            'fecha_modifica',
        ],
    ]) ?>

</div>
