<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_matricula".
 *
 * @property integer $id
 * @property integer $idfactura
 * @property string $idcarr
 * @property string $idmatricula
 * @property string $idasig
 * @property integer $nivel
 * @property string $paralelo
 * @property string $idnota
 * @property integer $credito
 * @property integer $vrepite
 * @property string $costo
 * @property integer $horario
 * @property string $fecha
 * @property integer $estado
 *
 * @property Factura $idfactura0
 * @property Notasalumnoasignatura $idnota0
 */
class DetalleMatricula extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_matricula';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idfactura', 'idmatricula', 'idasig'], 'required'],
            [['idfactura', 'nivel', 'idnota', 'credito', 'vrepite', 'horario', 'estado'], 'integer'],
            [['costo'], 'number'],
            [['fecha'], 'safe'],
            [['idcarr'], 'string', 'max' => 6],
            [['idmatricula'], 'string', 'max' => 20],
            [['idasig'], 'string', 'max' => 10],
            [['paralelo'], 'string', 'max' => 3],
            [['idfactura'], 'exist', 'skipOnError' => true, 'targetClass' => Factura::className(), 'targetAttribute' => ['idfactura' => 'id']],
            [['idnota'], 'exist', 'skipOnError' => true, 'targetClass' => Notasalumnoasignatura::className(), 'targetAttribute' => ['idnota' => 'idnaa']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idfactura' => 'Idfactura',
            'idcarr' => 'Idcarr',
            'idmatricula' => 'Idmatricula',
            'idasig' => 'Idasig',
            'nivel' => 'Nivel',
            'paralelo' => 'Paralelo',
            'idnota' => 'Idnota',
            'credito' => 'Credito',
            'vrepite' => 'Vrepite',
            'costo' => 'Costo',
            'horario' => 'Horario',
            'fecha' => 'Fecha',
            'estado' => 'Estado',
			'idFactura0.cedula'=> 'Cédula',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdFactura0()
    {
        return $this->hasOne(Factura::className(), ['id' => 'idfactura']);
    }

	public function getIdCarr0()
    {
        return $this->hasOne(Carrera::className(), ['idCarr' => 'idcarr']);
    }

	public function getCarrera()
    {
		$model=$this->idCarr0;
		return $model?$model->NombCarr:'';
    }	

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdnota0()
    {
        return $this->hasOne(Notasalumnoasignatura::className(), ['idnaa' => 'idnota']);
    }

	public function getIdAsig()
	{
		return $this->hasOne(Asignatura::className(), ['IdAsig' => 'idasig']);
	}

	public function getAsignatura()
	{
		$model=$this->idAsig;
		return $model?$model->NombAsig:'';
	}
	
	
	public function getAlumno()
	{
		$model=$this->idFactura0;
		return $model?$model->getAlumno():'';
	}


}
