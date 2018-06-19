<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "parametroscalificacion".
 *
 * @property integer $idparametroscalificacion
 * @property string $sigla
 * @property string $parametroscalificacion
 * @property integer $escala
 */
class Parametroscalificacion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parametroscalificacion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['escala'], 'integer'],
            [['sigla'], 'string', 'max' => 2],
            [['parametro'], 'string', 'max' => 245],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idparametro' => 'Id parametro',
            'sigla' => 'Sigla',
            'parametro' => 'Parametro',
            'escala' => 'Escala',
        ];
    }
}
