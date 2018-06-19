<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalle_horario".
 *
 * @property integer $id
 * @property integer $idhorario
 * @property integer $idcurso
 * @property string $dia
 * @property string $hora_inicio
 * @property string $hora_fin
 *
 * @property CursoOfertado $idcurso0
 * @property Horario $idhorario0
 */
class DetalleHorario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detalle_horario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idhorario', 'idcurso'], 'required'],
            [['idhorario', 'idcurso'], 'integer'],
            [['hora_inicio', 'hora_fin'], 'safe'],
            [['dia'], 'string', 'max' => 45],
            [['idcurso'], 'exist', 'skipOnError' => true, 'targetClass' => CursoOfertado::className(), 'targetAttribute' => ['idcurso' => 'id']],
            [['idhorario'], 'exist', 'skipOnError' => true, 'targetClass' => Horario::className(), 'targetAttribute' => ['idhorario' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idhorario' => 'Idhorario',
            'idcurso' => 'Idcurso',
            'dia' => 'Día',
            'hora_inicio' => 'Hora Inicio',
            'hora_fin' => 'Hora Fin',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdcurso0()
    {
        return $this->hasOne(CursoOfertado::className(), ['id' => 'idcurso']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdhorario0()
    {
        return $this->hasOne(Horario::className(), ['id' => 'idhorario']);
    }
}
