<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tokens;

/**
 * TokensSearch represents the model behind the search form of `app\models\Tokens`.
 */
class TokensSearch extends Tokens
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'decimals', 'chain_id'], 'integer'],
            [['name', 'denom', 'blockchain', 'originator', 'asset_id', 'lock_proxy_hash', 'image'], 'safe'],
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
        $query = Tokens::find();

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
            'decimals' => $this->decimals,
            'chain_id' => $this->chain_id,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'cast(denom as text)', $this->denom])
            ->andFilterWhere(['ilike', 'cast(blockchain as text)', $this->blockchain])
            ->andFilterWhere(['ilike', 'originator', $this->originator])
            ->andFilterWhere(['ilike', 'asset_id', $this->asset_id])
            ->andFilterWhere(['ilike', 'lock_proxy_hash', $this->lock_proxy_hash])
            ->andFilterWhere(['ilike', 'image', $this->image]);

        return $dataProvider;
    }
}
