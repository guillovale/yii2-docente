<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "curso_ofertado".
 *
 * @property integer $id
 * @property integer $idper
 * @property integer $iddetallemalla
 * @property string $iddocente
 * @property string $paralelo
 * @property integer $cupo
 * @property integer $idhorario
 * @property integer $estado
 * @property integer $restringido
 *
 * @property DetalleMalla $iddetallemalla0
 * @property DetalleHorario[] $detalleHorarios
 */
class CursoOfertado extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'curso_ofertado';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idper', 'iddetallemalla'], 'required'],
            [['idper', 'iddetallemalla', 'cupo', 'idhorario', 'estado', 'restringido'], 'integer'],
            [['iddocente'], 'string', 'max' => 20],
            [['paralelo'], 'string', 'max' => 2],
            [['iddetallemalla'], 'exist', 'skipOnError' => true, 'targetClass' => DetalleMalla::className(), 'targetAttribute' => ['iddetallemalla' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idper' => 'Id período',
            'iddetallemalla' => 'Iddetallemalla',
            'iddocente' => 'Iddocente',
            'paralelo' => 'Paralelo',
            'cupo' => 'Cupo',
            'idhorario' => 'Idhorario',
            'estado' => 'Estado',
            'restringido' => 'Restringido',
			'periodo.DescPerLec' => 'Período',
			'detallemalla.malla.carrera.NombCarr' => 'Carrera',
			'detallemalla.asignatura.NombAsig' => 'Asignatura',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getPeriodo()
    {
        return $this->hasOne(Periodolectivo::className(), ['idper' => 'idper']);
    }

    public function getDetallemalla()
    {
        return $this->hasOne(DetalleMalla::className(), ['id' => 'iddetallemalla']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalleHorarios()
    {
        return $this->hasMany(DetalleHorario::className(), ['idcurso' => 'id']);
    }

	public function getDocente()
    {
        return $this->hasOne(Informacionpersonald::className(), ['CIInfPer' => 'iddocente']);
    }

	public function getNombreDocente()
    {
		$model=$this->docente;
		return $model?($model->ApellInfPer . ' ' . $model->ApellMatInfPer . ' ' . $model->NombInfPer):'';
    }


}
