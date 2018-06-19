<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CursoOfertado;

/**
 * CursoOfertadoSearch represents the model behind the search form about `app\models\CursoOfertado`.
 */
class CursoOfertadoSearch extends CursoOfertado
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idper', 'iddetallemalla', 'cupo', 'idhorario', 'estado', 'restringido'], 'integer'],
            [['iddocente', 'paralelo'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$usuario = Yii::$app->user->identity;
		$cedula = $usuario?$usuario->CIInfPer:'0';
		$hoy = date('Y-m-d');
		$query = CursoOfertado::find()
				->joinWith('detallemalla')
				->where(['iddocente' =>$cedula])
						->orderBy(['idper'=>SORT_DESC, 'detalle_malla.idmalla'=>SORT_ASC, 'detalle_malla.nivel'=>SORT_ASC, 
							'detalle_malla.idasignatura'=>SORT_ASC, 'paralelo'=>SORT_ASC]);

       // $query = CursoOfertado::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'idper' => $this->idper,
            'iddetallemalla' => $this->iddetallemalla,
            'cupo' => $this->cupo,
            'idhorario' => $this->idhorario,
            'estado' => $this->estado,
            'restringido' => $this->restringido,
        ]);

        $query->andFilterWhere(['like', 'iddocente', $this->iddocente])
            ->andFilterWhere(['like', 'paralelo', $this->paralelo])
			->andFilterWhere(['>=', 'fecha_fin', $hoy]);

        return $dataProvider;
    }
}
