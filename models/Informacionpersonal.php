<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "informacionpersonal".
 *
 * @property string $CIInfPer
 * @property string $cedula_pasaporte
 * @property string $TipoDocInfPer
 * @property string $ApellInfPer
 * @property string $ApellMatInfPer
 * @property string $NombInfPer
 * @property string $NacionalidadPer
 * @property integer $EtniaPer
 * @property string $FechNacimPer
 * @property string $LugarNacimientoPer
 * @property string $GeneroPer
 * @property string $EstadoCivilPer
 * @property string $CiudadPer
 * @property string $DirecDomicilioPer
 * @property string $Telf1InfPer
 * @property string $CelularInfPer
 * @property string $TipoInfPer
 * @property integer $statusper
 * @property string $mailPer
 * @property string $mailInst
 * @property integer $GrupoSanguineo
 * @property string $tipo_discapacidad
 * @property string $carnet_conadis
 * @property string $num_carnet_conadis
 * @property integer $porcentaje_discapacidad
 * @property resource $fotografia
 * @property string $codigo_dactilar
 * @property integer $hd_posicion
 * @property resource $huella_dactilar
 * @property string $ultima_actualizacion
 * @property string $codigo_verificacion
 * @property integer $entregofichamedica
 * @property string $paisresidencia
 * @property integer $provinciaresidencia
 * @property integer $cantonresidencia
 *
 * @property AcademicoAlumno[] $academicoAlumnos
 * @property Factura[] $facturas
 * @property Ingreso[] $ingresos
 * @property Matricula[] $matriculas
 * @property Notasalumnoasignatura[] $notasalumnoasignaturas
 * @property Registrotitulos[] $registrotitulos
 */
class Informacionpersonal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informacionpersonal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['CIInfPer', 'cedula_pasaporte', 'carnet_conadis', 'num_carnet_conadis', 'porcentaje_discapacidad', 'codigo_dactilar', 'hd_posicion', 'huella_dactilar', 'ultima_actualizacion', 'codigo_verificacion'], 'required'],
            [['EtniaPer', 'statusper', 'GrupoSanguineo', 'porcentaje_discapacidad', 'hd_posicion', 'entregofichamedica', 'provinciaresidencia', 'cantonresidencia'], 'integer'],
            [['FechNacimPer', 'ultima_actualizacion'], 'safe'],
            [['fotografia', 'huella_dactilar'], 'string'],
            [['CIInfPer', 'num_carnet_conadis'], 'string', 'max' => 20],
            [['cedula_pasaporte'], 'string', 'max' => 13],
            [['TipoDocInfPer', 'GeneroPer', 'EstadoCivilPer', 'tipo_discapacidad'], 'string', 'max' => 1],
            [['ApellInfPer', 'ApellMatInfPer', 'NombInfPer', 'NacionalidadPer', 'CiudadPer'], 'string', 'max' => 45],
            [['LugarNacimientoPer'], 'string', 'max' => 120],
            [['DirecDomicilioPer', 'codigo_dactilar'], 'string', 'max' => 100],
            [['Telf1InfPer', 'CelularInfPer'], 'string', 'max' => 12],
            [['TipoInfPer', 'carnet_conadis', 'paisresidencia'], 'string', 'max' => 2],
            [['mailPer', 'mailInst'], 'string', 'max' => 60],
            [['codigo_verificacion'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CIInfPer' => 'Ciinf Per',
            'cedula_pasaporte' => 'Cedula Pasaporte',
            'TipoDocInfPer' => 'Tipo Doc Inf Per',
            'ApellInfPer' => 'Apell Inf Per',
            'ApellMatInfPer' => 'Apell Mat Inf Per',
            'NombInfPer' => 'Nomb Inf Per',
            'NacionalidadPer' => 'Nacionalidad Per',
            'EtniaPer' => 'Etnia Per',
            'FechNacimPer' => 'Fech Nacim Per',
            'LugarNacimientoPer' => 'Lugar Nacimiento Per',
            'GeneroPer' => 'Genero Per',
            'EstadoCivilPer' => 'Estado Civil Per',
            'CiudadPer' => 'Ciudad Per',
            'DirecDomicilioPer' => 'Direc Domicilio Per',
            'Telf1InfPer' => 'Telf1 Inf Per',
            'CelularInfPer' => 'Celular Inf Per',
            'TipoInfPer' => 'Tipo Inf Per',
            'statusper' => 'Statusper',
            'mailPer' => 'Mail Per',
            'mailInst' => 'Mail Inst',
            'GrupoSanguineo' => 'Grupo Sanguineo',
            'tipo_discapacidad' => 'Tipo Discapacidad',
            'carnet_conadis' => 'Carnet Conadis',
            'num_carnet_conadis' => 'Num Carnet Conadis',
            'porcentaje_discapacidad' => 'Porcentaje Discapacidad',
            'fotografia' => 'Fotografia',
            'codigo_dactilar' => 'Codigo Dactilar',
            'hd_posicion' => 'Hd Posicion',
            'huella_dactilar' => 'Huella Dactilar',
            'ultima_actualizacion' => 'Ultima Actualizacion',
            'codigo_verificacion' => 'Codigo Verificacion',
            'entregofichamedica' => 'Entregofichamedica',
            'paisresidencia' => 'Paisresidencia',
            'provinciaresidencia' => 'Provinciaresidencia',
            'cantonresidencia' => 'Cantonresidencia',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicoAlumnos()
    {
        return $this->hasMany(AcademicoAlumno::className(), ['CIInfPer' => 'CIInfPer']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Factura::className(), ['cedula' => 'CIInfPer']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngresos()
    {
        return $this->hasMany(Ingreso::className(), ['CIInfPer' => 'CIInfPer']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatriculas()
    {
        return $this->hasMany(Matricula::className(), ['CIInfPer' => 'CIInfPer']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotasalumnoasignaturas()
    {
        return $this->hasMany(Notasalumnoasignatura::className(), ['CIInfPer' => 'CIInfPer']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrotitulos()
    {
        return $this->hasMany(Registrotitulos::className(), ['ciinfper' => 'CIInfPer']);
    }
}
