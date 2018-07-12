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
#$pdf = 'uploads/'.$modelcurso->id.'.'.'pdf';
$pos = strrpos($modelcurso->silabo, '.');
$ext = substr($modelcurso->silabo, $pos, strlen($modelcurso->silabo));
$pdf = 'uploads/silabos/'.$modelcurso->id.$ext; 

?>
<div class="curso-ofertado-index">

    <h3><?= Html::encode($this->title) ?></h3>
	<address>
		C.I.: <?= $this->params['cedula'] ?> <br>
		<b>Docente: <?= $this->params['docente'] ?> </b><br>
		<b>Asignatura: <?= $this->params['asignatura'] ?> </b><br><br>
		Sílabo: <a href="http://190.152.10.220/academico/web/uploads/silabos/<?php echo $modelcurso->id.$ext ?> " download="newfilename"><?php echo $modelcurso->silabo ?></a>  
		<?php 
			#echo Html::a($modelcurso->silabo, [$pdf,'id' => $model->imageFile]);
		$tempFilename = $_FILES;
		#$path = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		#echo var_dump(@web, $_SERVER['HTTP_HOST']);
		?> <br>
	</address>
   
<p>

<br><br>
</p>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?php #$form->field($model, 'imageFile')->fileInput() 
    ?>

    <!-- <button>Subir</button> -->

<?php ActiveForm::end() ?>
    
</div>
