<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Summary;
use yii\db\Expression;

/**
 * SummarySearch represents the model behind the search form of `app\models\Summary`.
 */
class SummarySearch extends Summary
{

    public $varPreciseValues=false;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet', 'external_in', 'external_out','varPreciseValues','rewards_value'], 'safe'],
            [['wait_unbonding_value', 'unbonding_value'], 'number'],
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
        $query = Summary::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => [
                'rewards_value' => SORT_DESC,
                //'unbonding_value' => SORT_DESC,
                //'wait_unbonding_value' => SORT_DESC,
            ]],
        ]);

        $dataProvider->sort->attributes['wait_unbonding_value']=[
            'asc'=>['coalesce("wait_unbonding_value",0)'=>SORT_ASC],
            'desc'=>['coalesce("wait_unbonding_value",0)'=>SORT_DESC],
        ];

        $dataProvider->sort->attributes['unbonding_value']=[
            'asc'=>['coalesce("unbonding_value",0)'=>SORT_ASC],
            'desc'=>['coalesce("unbonding_value",0)'=>SORT_DESC],
        ];

        $dataProvider->sort->attributes['staked_value']=[
            'asc'=>['coalesce("staked_value",0)'=>SORT_ASC],
            'desc'=>['coalesce("staked_value",0)'=>SORT_DESC],
        ];


        $dataProvider->sort->attributes['rewards_value']=[
            'asc'=>['coalesce(cast("rewards_value"->>\'swth\' as numeric),0)'=>SORT_ASC],
            'desc'=>['coalesce(cast("rewards_value"->>\'swth\' as numeric),0)'=>SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'wait_unbonding_value' => $this->wait_unbonding_value,
            'unbonding_value' => $this->unbonding_value,
            'rewards_value' => $this->rewards_value,
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet])
            ->andFilterWhere(['ilike', 'external_in', $this->external_in])
            ->andFilterWhere(['ilike', 'external_out', $this->external_out]);

        return $dataProvider;
    }
}
