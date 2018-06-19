<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "componentescalificacion".
 *
 * @property integer $idcomponentescalificacion
 * @property integer $idparametroscalificacion
 * @property string $componente
 * @property string $tipo
 */
class Componentescalificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'componentescalificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idparametro'], 'integer'],
            [['componente'], 'string', 'max' => 200],
            [['tipo'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcomponente' => 'id Componente',
            'idparametro' => 'Id Parametro',
            'componente' => 'Componente',
            'tipo' => 'Tipo',
        ];
    }

	public function getIdParam()
	{
		return $this->hasOne(Parametroscalificacion::className(), ['idparametro' => 'idparametro']);
	}

	public function getNomparametro()
	{
		$model=$this->idParam;
		return $model?$model->idparametro:'';
	}

}
