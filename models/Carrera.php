<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "carrera".
 *
 * @property string $idCarr
 * @property string $NombCarr
 * @property string $nivelCarr
 * @property integer $StatusCarr
 * @property string $codCarr_senescyt
 * @property integer $mod_id
 * @property string $sau_id
 * @property integer $id_tc
 * @property string $inst_cod
 * @property string $idcarr_utelvt
 * @property integer $idsede
 * @property integer $idfacultad
 * @property integer $culminacion
 * @property integer $optativa
 * @property string $carreracol
 * @property integer $habilitada
 * @property string $tituloh
 * @property string $titulom
 * @property integer $folio
 * @property integer $cantidadestudiante
 * @property integer $cantidadporpagina
 * @property integer $cantidadlibro
 *
 * @property Matricula[] $matriculas
 * @property Registrotitulos[] $registrotitulos
 */
class Carrera extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carrera';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idCarr', 'StatusCarr', 'codCarr_senescyt', 'mod_id', 'sau_id', 'id_tc', 'inst_cod', 'idcarr_utelvt'], 'required'],
            [['StatusCarr', 'mod_id', 'id_tc', 'idsede', 'idfacultad', 'culminacion', 'optativa', 'habilitada', 'folio', 'cantidadestudiante', 'cantidadporpagina', 'cantidadlibro'], 'integer'],
            [['idCarr'], 'string', 'max' => 6],
            [['NombCarr', 'carreracol'], 'string', 'max' => 45],
            [['nivelCarr'], 'string', 'max' => 20],
            [['codCarr_senescyt'], 'string', 'max' => 8],
            [['sau_id'], 'string', 'max' => 4],
            [['inst_cod'], 'string', 'max' => 12],
            [['idcarr_utelvt'], 'string', 'max' => 10],
            [['tituloh', 'titulom'], 'string', 'max' => 245],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idCarr' => 'Id Carr',
            'NombCarr' => 'Nomb Carr',
            'nivelCarr' => 'Nivel Carr',
            'StatusCarr' => 'Status Carr',
            'codCarr_senescyt' => 'Cod Carr Senescyt',
            'mod_id' => 'Mod ID',
            'sau_id' => 'Sau ID',
            'id_tc' => 'Id Tc',
            'inst_cod' => 'Inst Cod',
            'idcarr_utelvt' => 'Idcarr Utelvt',
            'idsede' => 'Idsede',
            'idfacultad' => 'Idfacultad',
            'culminacion' => 'Culminacion',
            'optativa' => 'Optativa',
            'carreracol' => 'Carreracol',
            'habilitada' => 'Habilitada',
            'tituloh' => 'Tituloh',
            'titulom' => 'Titulom',
            'folio' => 'Folio',
            'cantidadestudiante' => 'Cantidadestudiante',
            'cantidadporpagina' => 'Cantidadporpagina',
            'cantidadlibro' => 'Cantidadlibro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatriculas()
    {
        return $this->hasMany(Matricula::className(), ['idCarr' => 'idCarr']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrotitulos()
    {
        return $this->hasMany(Registrotitulos::className(), ['idcarr' => 'idCarr']);
    }
}
