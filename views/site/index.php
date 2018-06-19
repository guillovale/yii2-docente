<?php
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = 'UTELVT-DOCENTE';
$cedula = Yii::$app->user->identity?Yii::$app->user->identity->CIInfPer:'';
//Html::img('@web/uploads/banner_utelvt.jpg', ['alt'=>'some', 'class'=>'thing']);
?>
<div class="site-index">
	
	<?php if (Yii::$app->session->hasFlash('success')): ?>
	  <div class="alert alert-success alert-dismissable">
	  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
	  <h4><i class="icon fa fa-check"></i>Actualizado!</h4>
	  <?= Yii::$app->session->getFlash('success') ?>
	  </div>
	<?php endif; ?>

    <div class="jumbotron">
	
	<?php 
		$images=['<img src="http://190.152.10.220/docente/web/uploads/banner_utelvt.jpg"/>',
			'<img src="http://utelvt.edu.ec/sitioweb/images/banner1.jpg"/>',
			'<img src="http://utelvt.edu.ec/sitioweb/images/banner2.jpg"/>',
			'<img src="http://utelvt.edu.ec/sitioweb/images/banner3.jpg"/>',
			'<img src="http://utelvt.edu.ec/sitioweb/images/banner4.jpg"/>',
			'<img src="http://utelvt.edu.ec/sitioweb/images/banner5.jpg"/>']; 
		echo yii\bootstrap\Carousel::widget(['items'=>$images]);
	?>

    </div>

    <div class="body-content">

        <div class="row">
            
                <h3>Estimado Docente:</h3>

                <p>Para el registro de calificaciones de las asignaturas, tener en cuenta que se contabilizará en escala de cero (0) a diez (10) puntos (art.72 Regl. Reg. Acad.) y será siempre en cifras correspondientes a números enteros.

Se deberá registrar como mínimo un componente A, un componente B, un componente C, la asistencia y el examen final (art.60,61 Regl. Reg. Acad.). El sistema directamente realizará el cálculo correspondiente.</p>

                <p><b>Nota: Por seguridad recomendamos cambiar su clave inicial y cerrar sesión al terminar de utilizar el módulo.</b></p>
		

    </div>
</div>
