<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NotaFinal;

/**
 * NotaFinalSearch represents the model behind the search form of `app\models\NotaFinal`.
 */
class NotaFinalSearch extends NotaFinal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'idmatricula', 'estado'], 'integer'],
            [['fecha', 'tipo_estado', 'observacion', 'usuario_modifica', 'fecha_modifica'], 'safe'],
            [['A1', 'A2', 'B1', 'B2', 'C1', 'C2', 'AS1', 'AS2', 'X1', 'X2', 'RC', 'CF', 'ASF'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = NotaFinal::find();

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
            'idmatricula' => $this->idmatricula,
            'fecha' => $this->fecha,
            'A1' => $this->A1,
            'A2' => $this->A2,
            'B1' => $this->B1,
            'B2' => $this->B2,
            'C1' => $this->C1,
            'C2' => $this->C2,
            'AS1' => $this->AS1,
            'AS2' => $this->AS2,
            'X1' => $this->X1,
            'X2' => $this->X2,
            'RC' => $this->RC,
            'CF' => $this->CF,
            'ASF' => $this->ASF,
            'estado' => $this->estado,
            'fecha_modifica' => $this->fecha_modifica,
        ]);

        $query->andFilterWhere(['like', 'tipo_estado', $this->tipo_estado])
            ->andFilterWhere(['like', 'observacion', $this->observacion])
            ->andFilterWhere(['like', 'usuario_modifica', $this->usuario_modifica]);

        return $dataProvider;
    }
}
