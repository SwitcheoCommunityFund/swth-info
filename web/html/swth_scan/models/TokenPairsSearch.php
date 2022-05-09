<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TokenPairs;

/**
 * TokenPairsSearch represents the model behind the search form of `app\models\TokenPairs`.
 */
class TokenPairsSearch extends TokenPairs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'system', 'token0_symbol', 'token0_id', 'token1_symbol', 'token1_id', 'updated'], 'safe'],
            [['reserve_usd', 'token0_price', 'reserve0', 'token1_price', 'reserve1'], 'number'],
            [['token0_decimals', 'token1_decimals'], 'integer'],
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
        $query = TokenPairs::find();

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
            'reserve_usd' => $this->reserve_usd,
            'token0_price' => $this->token0_price,
            'token0_decimals' => $this->token0_decimals,
            'reserve0' => $this->reserve0,
            'token1_price' => $this->token1_price,
            'token1_decimals' => $this->token1_decimals,
            'reserve1' => $this->reserve1,
            'updated' => $this->updated,
        ]);

        $query->andFilterWhere(['ilike', 'id', $this->id])
            ->andFilterWhere(['ilike', 'system', $this->system])
            ->andFilterWhere(['ilike', 'token0_symbol', $this->token0_symbol])
            ->andFilterWhere(['ilike', 'token0_id', $this->token0_id])
            ->andFilterWhere(['ilike', 'token1_symbol', $this->token1_symbol])
            ->andFilterWhere(['ilike', 'token1_id', $this->token1_id]);

        return $dataProvider;
    }
}
