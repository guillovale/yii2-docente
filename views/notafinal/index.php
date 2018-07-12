<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotaFinalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nota Finals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nota-final-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Nota Final', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idmatricula',
            'fecha',
            'A1',
            'A2',
            //'B1',
            //'B2',
            //'C1',
            //'C2',
            //'AS1',
            //'AS2',
            //'X1',
            //'X2',
            //'RC',
            //'CF',
            //'ASF',
            //'estado',
            //'tipo_estado',
            //'observacion',
            //'usuario_modifica',
            //'fecha_modifica',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
