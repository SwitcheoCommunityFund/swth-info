<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ChainDenomDecimal;

/**
 * ChainDenomDecimalSearch represents the model behind the search form of `app\models\ChainDenomDecimal`.
 */
class ChainDenomDecimalSearch extends ChainDenomDecimal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'decimal'], 'integer'],
            [['blockchain', 'denom', 'full_name'], 'safe'],
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
        $query = ChainDenomDecimal::find();

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
            'decimal' => $this->decimal,
        ]);

        $query->andFilterWhere(['ilike', 'blockchain', $this->blockchain])
            ->andFilterWhere(['ilike', 'denom', $this->denom])
            ->andFilterWhere(['ilike', 'full_name', $this->full_name]);

        return $dataProvider;
    }
}
