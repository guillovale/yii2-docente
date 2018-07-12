<?php

use yii\helpers\Html;
#use yii\widgets\ActiveForm;
use kartik\builder\TabularForm;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\models\NotaFinal */
/* @var $form yii\widgets\ActiveForm */
?>



<div class="nota-final-form">

<?php
$createUrl = "imprimir";
$deleteUrl = "delete";
$form = ActiveForm::begin();
echo TabularForm::widget([
    'form' => $form,
    'dataProvider' => $dataProvider,
	'actionColumn' => false,
    'attributes' => [
        'cedula' => ['type' => TabularForm::INPUT_STATIC],
		'nombre' => ['type' => TabularForm::INPUT_STATIC],
        #'idcurso' => [
         #   'type' => TabularForm::INPUT_WIDGET, 
         #   'widgetClass' => \kartik\widgets\ColorInput::classname()
        #],
        #'iddocente' => [
        #    'type' => TabularForm::INPUT_DROPDOWN_LIST, 
            #'items'=>ArrayHelper::map(Author::find()->orderBy('name')->asArray()->all(), 'id', 'name')
        #],
        'NGA1' => [
            'type' => TabularForm::INPUT_STATIC, 
			'label' => 'Ga1',
            #'options'=>['class'=>'form-control text-right'], 
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
        'NGA2' => [
            'type' => TabularForm::INPUT_STATIC, 
			'label' => 'Ga2',
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'NPA' => [
            'type' => TabularForm::INPUT_STATIC, 
			'label' => 'Pa',
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'NX1' => [
            'type' => TabularForm::INPUT_STATIC, 
			'label' => 'Ex1',
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'NX2' => [
            'type' => TabularForm::INPUT_STATIC,
			'label' => 'Ex2', 
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'NM' => [
            'type' => TabularForm::INPUT_STATIC,
			'label' => 'Mej.',
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'NAT' => [
            'type' => TabularForm::INPUT_STATIC, 
			'label' => 'Ast.',
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'notafinal' => [
            'type' => TabularForm::INPUT_STATIC, 
			#'label' => 'Final',
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
		'Estado' => [
            'type' => TabularForm::INPUT_STATIC, 
            'columnOptions'=>['hAlign'=>GridView::ALIGN_RIGHT]
        ],
    ],
    'gridSettings' => [
        'floatHeader' => true,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i> Consolidado</h3>',
            'type' => GridView::TYPE_PRIMARY,
			
            'after'=> 
                Html::a(
                    '<i class="glyphicon glyphicon-print"></i> Imprimir', 
                    ['imprimir', 'idcurso'=>$idcurso], 
                    ['class'=>'btn btn-success']
                )# . '&nbsp;' . 
                #Html::a(
                #    '<i class="glyphicon glyphicon-remove"></i> Delete', 
                #    $deleteUrl, 
                #    ['class'=>'btn btn-danger']
                #) . '&nbsp;' .
                #Html::submitButton(
                #    '<i class="glyphicon glyphicon-floppy-disk"></i> Save', 
                #    ['class'=>'btn btn-primary']
                #)
        ]
    ]     
]); 
ActiveForm::end(); 

?>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'idmatricula')->textInput() ?>

    <?= $form->field($model, 'fecha')->textInput() ?>

    <?= $form->field($model, 'A1')->textInput() ?>

    <?= $form->field($model, 'A2')->textInput() ?>

    <?= $form->field($model, 'B1')->textInput() ?>

    <?= $form->field($model, 'B2')->textInput() ?>

    <?= $form->field($model, 'C1')->textInput() ?>

    <?= $form->field($model, 'C2')->textInput() ?>

    <?= $form->field($model, 'AS1')->textInput() ?>

    <?= $form->field($model, 'AS2')->textInput() ?>

    <?= $form->field($model, 'X1')->textInput() ?>

    <?= $form->field($model, 'X2')->textInput() ?>

    <?= $form->field($model, 'RC')->textInput() ?>

    <?= $form->field($model, 'CF')->textInput() ?>

    <?= $form->field($model, 'ASF')->textInput() ?>

    <?= $form->field($model, 'estado')->textInput() ?>

    <?= $form->field($model, 'tipo_estado')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'observacion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'usuario_modifica')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_modifica')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
