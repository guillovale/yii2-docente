<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "malla_carrera".
 *
 * @property integer $id
 * @property string $idcarrera
 * @property string $detalle
 * @property string $fecha
 * @property string $anio
 * @property integer $estado
 *
 * @property DetalleMalla[] $detalleMallas
 * @property Carrera $idcarrera0
 */
class MallaCarrera extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'malla_carrera';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcarrera', 'detalle', 'fecha', 'anio'], 'required'],
            [['fecha'], 'safe'],
            [['estado'], 'integer'],
            [['idcarrera'], 'string', 'max' => 6],
            [['detalle'], 'string', 'max' => 100],
            [['anio'], 'string', 'max' => 4],
            [['idcarrera'], 'exist', 'skipOnError' => true, 'targetClass' => Carrera::className(), 'targetAttribute' => ['idcarrera' => 'idCarr']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idcarrera' => 'Idcarrera',
            'detalle' => 'Detalle',
            'fecha' => 'Fecha',
            'anio' => 'Anio',
            'estado' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleMallas()
    {
        return $this->hasMany(DetalleMalla::className(), ['idmalla' => 'id']);
    }
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarrera()
    {
        return $this->hasOne(Carrera::className(), ['idCarr' => 'idcarrera']);
    }
}
