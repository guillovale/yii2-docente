<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "libreta_calificacion".
 *
 * @property integer $id
 * @property integer $idper
 * @property string $iddocente
 * @property string $fecha
 * @property integer $hemisemestre
 * @property integer $idparametro
 * @property integer $idcomponente
 * @property string $tema
 *
 * @property InformacionpersonalD $iddocente0
 * @property Periodolectivo $idper0
 */
class LibretaCalificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'libreta_calificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idper', 'iddocente', 'fecha', 'hemisemestre', 'idcomponente', 'idcurso'], 'required'],
            [['idper', 'hemisemestre', 'idcomponente', 'idcomponente', 'idcurso'], 'integer'],
            [['fecha'], 'safe'],
            [['iddocente'], 'string', 'max' => 20],
            [['tema'], 'string', 'max' => 200],
            [['iddocente'], 'exist', 'skipOnError' => true, 'targetClass' => Informacionpersonald::className(), 'targetAttribute' => ['iddocente' => 'CIInfPer']],
            [['idper'], 'exist', 'skipOnError' => true, 'targetClass' => Periodolectivo::className(), 'targetAttribute' => ['idper' => 'idper']],
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
			'idcurso' => 'Curso',
            'iddocente' => 'Iddocente',
            'fecha' => 'Fecha',
            'hemisemestre' => 'Hemisemestre',
            'idparametro' => 'Parámetro',
            'idcomponente' => 'Componente',
            'tema' => 'Tema',
			'parametro' => 'Parámetro',
			'componente' => 'Componente',
			'componente0.idParam.parametro' => 'Parámetro',
        ];
    }

    /**
     * @return \yii\db\AcomponentectiveQuery
     */
    public function getIddocente0()
    {
        return $this->hasOne(Informacionpersonald::className(), ['CIInfPer' => 'iddocente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
	public function getCurso()
    {
        return $this->hasOne(CursoOfertado::className(), ['id' => 'idcurso']);
    }

    public function getIdper0()
    {
        return $this->hasOne(Periodolectivo::className(), ['idper' => 'idper']);
    }

	public function getParametro0()
    {
        return $this->hasOne(Parametroscalificacion::className(), ['idparametro' => 'idparametro']);
    }

	public function getParametro()
    {
        $model=$this->parametro0;
		return $model?$model->parametro:'';
    }
	public function getParametrosigla()
    {
        $model=$this->parametro0;
		return $model?$model->sigla:'';
    }
	public function getComponente0()
    {
        return $this->hasOne(Componentescalificacion::className(), ['idcomponente' => 'idcomponente']);
    }

	public function getComponente()
    {
        $model=$this->componente0;
		return $model?$model->componente:'';
    }
	
	public function getAsignatura()
    {
        $notasdetalle = NotasDetalle::find()->where(['idlibreta' => $this->id])->one();
		return $notasdetalle?$notasdetalle->getIdasig():'';
    }
}
