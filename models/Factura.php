<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "factura".
 *
 * @property integer $id
 * @property string $cedula
 * @property integer $idper
 * @property string $tipo_documento
 * @property string $fecha
 * @property string $iva
 * @property string $descuento
 * @property string $total
 * @property string $documento
 * @property string $pago
 * @property string $observacion
 * @property string $usuario
 *
 * @property AbonoFactura[] $abonoFacturas
 * @property DetalleMatricula[] $detalleMatriculas
 * @property Informacionpersonal $cedula0
 */
class Factura extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'factura';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cedula', 'idper'], 'required'],
            [['idper'], 'integer'],
            [['fecha'], 'safe'],
            [['iva', 'descuento', 'total', 'pago'], 'number'],
            [['cedula', 'usuario'], 'string', 'max' => 20],
            [['tipo_documento', 'documento', 'observacion'], 'string', 'max' => 50],
            [['cedula'], 'exist', 'skipOnError' => true, 'targetClass' => Informacionpersonal::className(), 'targetAttribute' => ['cedula' => 'CIInfPer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cedula' => 'Cedula',
            'idper' => 'Idper',
            'tipo_documento' => 'Tipo Documento',
            'fecha' => 'Fecha',
            'iva' => 'Iva',
            'descuento' => 'Descuento',
            'total' => 'Total',
            'documento' => 'Documento',
            'pago' => 'Pago',
            'observacion' => 'Observacion',
            'usuario' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAbonoFacturas()
    {
        return $this->hasMany(AbonoFactura::className(), ['idfactura' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleMatriculas()
    {
        return $this->hasMany(DetalleMatricula::className(), ['idfactura' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCedula0()
    {
        return $this->hasOne(Informacionpersonal::className(), ['CIInfPer' => 'cedula']);
    }

	public function getAlumno()
	{
		$model=$this->cedula0;
		return $model?($model->ApellInfPer.' '.$model->ApellMatInfPer.' '.$model->NombInfPer):'';
	}
}
