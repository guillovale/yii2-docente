<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CursoOfertadoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Archivo sílabo:';
$this->params['breadcrumbs'][] = ['label' => 'Asignaturas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cedula =  $this->params['cedula'];
$docente =  $this->params['docente'];
$asignatura = $this->params['asignatura'];
$pdf = 'uploads/'.$modelcurso->id.'.'.'pdf';
?>
<div class="curso-ofertado-index">

    <h3><?= Html::encode($this->title) ?></h3>
	<address>
		C.I.: <?= $this->params['cedula'] ?> <br>
		<b>Docente: <?= $this->params['docente'] ?> </b><br>
		<b>Asignatura: <?= $this->params['asignatura'] ?> </b><br>
	</address>
	<!-- <a href="./uploads/7953.pdf" download="newfilename">Download the pdf</a>-->

    <?php
	echo Html::a($modelcurso->silabo, [$pdf,'id' => $model->imageFile]);
	$tempFilename = $_FILES;
	#echo var_dump($model->imageFile);

?>
<p>

<br><br>
</p>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'imageFile')->fileInput() ?>

    <button>Subir</button>

<?php ActiveForm::end() ?>
    
</div>
