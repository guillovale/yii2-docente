<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notas_detalle".
 *
 * @property integer $idnota
 * @property integer $iddetallematricula
 * @property integer $idlibreta
 * @property string $nota
 */
class NotasDetalle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	public $cedula;
	public $estudiante;
	public $CompA1;
	public $CompB1;
	public $CompC1;
	public $EXM1;
	public $Ast1;
	public $A1;
	public $B1;
	public $C1;
	public $Ex1;
	public $As1;

    public static function tableName()
    {
        return 'notas_detalle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iddetallematricula', 'idlibreta'], 'integer'],
		['nota', 'integer', 'integerOnly' => true, 'min' => 0, 'max' => 10],
		// validates if age is greater than or equal to 30
		//['nota', 'compare', 'compareValue' => 10, 'operator' => '>=', 'type' => 'integer'],
            //[['nota'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idnota' => 'Id nota',
            'iddetallematricula' => 'Id detallematricula',
            'idlibreta' => 'Id libreta',
            'nota' => 'Nota',
		'cedula0' => 'Cédula',
        ];
    }

	public function getIddetalle()
	{
		return $this->hasOne(DetalleMatricula::className(), ['id' => 'iddetallematricula']);
	}

	public function getIdLibreta()
	{
		return $this->hasOne(LibretaCalificacion::className(), ['id' => 'idlibreta']);
	}
	 public function getIdFactura0()
	 {
		$model=$this->iddetalle;
		//echo var_dump($model); exit;
		return $this->hasOne(Factura::className(), ['id' =>'idfactura']);
	}

	public function getParametro()
	{
		$model=$this->componente;
		if ($model) {
			$parametro = Parametroscalificacion::find()->where(['idparametro'=> $model->idparametro])->all();
		}
		//echo var_dump($model); exit;
		return $parametro?$parametro[0]['parametro']:'';
	}

	public function getComponente()
	{
		$model=$this->idLibreta;
		if ($model) {
			$componente = Componentescalificacion::find()->where(['idcomponente'=> $model->idcomponente])->all();
		}
		//echo var_dump($model); exit;
		return $componente?$componente[0]['componente']:'';
	}

	public function getHemisemestre()
	{
		$model=$this->idLibreta;
		return $model?$model->hemisemestre:'';
	}

	public function getAlumno()
	{
		$model=$this->iddetalle;
		$idfactura = $model?$model->idfactura:'';
		$factura = Factura::find()->where(['id'=> $idfactura])->all();
		$cedula = $factura?$factura[0]['cedula']:'';
		$alumno = Informacionpersonal::find()->where(['CIInfPer'=> $cedula])->all();
		return $alumno?($alumno[0]['ApellInfPer'].' '.$alumno[0]['ApellMatInfPer'].' '.$alumno[0]['NombInfPer']):'';
		//return $factura?$factura[0]['cedula']:'';
	}
	public function getCedula0()
	{
		$model=$this->iddetalle;
		$idfactura = $model?$model->idfactura:'';
		$factura = Factura::find()->where(['id'=> $idfactura])->all();
		//$cedula = $factura?$factura[0]['cedula']:'';
		//$alumno = Informacionpersonal::find()->where(['CIInfPer'=> $cedula])->all();
		//return $alumno?($alumno[0]['ApellInfPer'].' '.$alumno[0]['ApellMatInfPer'].' '.$alumno[0]['NombInfPer']):'';
		return $factura?$factura[0]['cedula']:'';
	}
	
	public function getIdasig()
	{
		$model=$this->iddetalle;
		//echo var_dump($model); exit;
		$idasig = $model?$model->idasig:'';
		$asignatura = Asignatura::find()->where(['idAsig'=> $idasig])->one();
		return $asignatura?$asignatura->NombAsig:'';
	}

}
