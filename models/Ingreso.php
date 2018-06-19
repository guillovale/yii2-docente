<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ingreso".
 *
 * @property integer $id
 * @property integer $idper
 * @property string $idcarr
 * @property integer $idmalla
 * @property string $malla
 * @property string $CIInfPer
 * @property string $fecha
 * @property string $tipo_ingreso
 * @property string $observacion
 * @property string $usuario
 *
 * @property Informacionpersonal $cIInfPer
 */
class Ingreso extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ingreso';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idper', 'idcarr', 'malla', 'CIInfPer', 'fecha'], 'required'],
            [['idper', 'idmalla'], 'integer'],
            [['fecha'], 'safe'],
            [['idcarr'], 'string', 'max' => 6],
            [['malla'], 'string', 'max' => 10],
            [['CIInfPer'], 'string', 'max' => 20],
            [['tipo_ingreso'], 'string', 'max' => 4],
            [['observacion'], 'string', 'max' => 200],
            [['usuario'], 'string', 'max' => 30],
            [['CIInfPer'], 'exist', 'skipOnError' => true, 'targetClass' => Informacionpersonal::className(), 'targetAttribute' => ['CIInfPer' => 'CIInfPer']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idper' => 'Idper',
            'idcarr' => 'Idcarr',
            'idmalla' => 'Idmalla',
            'malla' => 'Malla',
            'CIInfPer' => 'Ciinf Per',
            'fecha' => 'Fecha',
            'tipo_ingreso' => 'Tipo Ingreso',
            'observacion' => 'Observacion',
            'usuario' => 'Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCIInfPer()
    {
        return $this->hasOne(Informacionpersonal::className(), ['CIInfPer' => 'CIInfPer']);
    }

	public function getMalla()
    {
        return $this->hasOne(MallaCarrera::className(), ['id' => 'idmalla']);
    }

}
