<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nota_final".
 *
 * @property int $id
 * @property int $idmatricula
 * @property string $fecha
 * @property double $A1
 * @property double $A2
 * @property double $B1
 * @property double $B2
 * @property double $C1
 * @property double $C2
 * @property double $AS1
 * @property double $AS2
 * @property double $X1
 * @property double $X2
 * @property double $RC
 * @property double $CF
 * @property double $ASF
 * @property int $estado
 * @property string $tipo_estado
 * @property string $observacion
 * @property string $usuario_modifica
 * @property string $fecha_modifica
 *
 * @property DetalleMatricula $matricula
 */
class NotaFinal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nota_final';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idmatricula'], 'required'],
            [['idmatricula', 'estado'], 'integer'],
            [['fecha', 'fecha_modifica'], 'safe'],
            [['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'AS1', 'AS2', 'X1', 'X2', 'RC', 'CF', 'ASF'], 'number'],
            [['tipo_estado'], 'string', 'max' => 50],
            [['observacion'], 'string', 'max' => 200],
            [['usuario_modifica'], 'string', 'max' => 20],
            [['idmatricula'], 'exist', 'skipOnError' => true, 'targetClass' => DetalleMatricula::className(), 'targetAttribute' => ['idmatricula' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idmatricula' => 'Idmatricula',
            'fecha' => 'Fecha',
            'A1' => 'A1',
            'A2' => 'A2',
            'B1' => 'B1',
            'B2' => 'B2',
            'C1' => 'C1',
            'C2' => 'C2',
            'AS1' => 'As1',
            'AS2' => 'As2',
            'X1' => 'X1',
            'X2' => 'X2',
            'RC' => 'Rc',
            'CF' => 'Cf',
            'ASF' => 'Asf',
            'estado' => 'Estado',
            'tipo_estado' => 'Tipo Estado',
            'observacion' => 'Observacion',
            'usuario_modifica' => 'Usuario Modifica',
            'fecha_modifica' => 'Fecha Modifica',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatricula()
    {
        return $this->hasOne(DetalleMatricula::className(), ['id' => 'idmatricula']);
    }
}
