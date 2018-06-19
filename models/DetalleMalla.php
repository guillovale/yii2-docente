<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_malla".
 *
 * @property integer $id
 * @property integer $idmalla
 * @property string $idasignatura
 * @property integer $nivel
 * @property integer $credito
 * @property string $caracter
 * @property integer $estado
 *
 * @property CursoOfertado[] $cursoOfertados
 * @property MallaCarrera $idmalla0
 */
class DetalleMalla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_malla';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idmalla', 'idasignatura'], 'required'],
            [['idmalla', 'nivel', 'credito', 'estado'], 'integer'],
            [['idasignatura'], 'string', 'max' => 10],
            [['caracter'], 'string', 'max' => 20],
            [['idmalla'], 'exist', 'skipOnError' => true, 'targetClass' => MallaCarrera::className(), 'targetAttribute' => ['idmalla' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idmalla' => 'Idmalla',
            'idasignatura' => 'Idasignatura',
            'nivel' => 'Nivel',
            'credito' => 'Credito',
            'caracter' => 'Caracter',
            'estado' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCursoOfertados()
    {
        return $this->hasMany(CursoOfertado::className(), ['iddetallemalla' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMalla()
    {
        return $this->hasOne(MallaCarrera::className(), ['id' => 'idmalla']);
    }
	
	public function getAsignatura()
    {
        return $this->hasOne(Asignatura::className(), ['IdAsig' => 'idasignatura']);
    }

}
