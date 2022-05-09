<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UnstakingStaked;

/**
 * UnstakingStakedSearch represents the model behind the search form of `app\models\UnstakingStaked`.
 */
class UnstakingStakedSearch extends UnstakingStaked
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'wallet', 'delegation_balance'], 'safe'],
            [['value', 'value_delegated', 'percent'], 'number'],
            [['count_delegated'], 'integer'],
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
        $query = UnstakingStaked::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder' => ['date' => SORT_DESC]
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'date' => $this->date,
            'value' => $this->value,
            'value_delegated' => $this->value_delegated,
            'count_delegated' => $this->count_delegated,
            'percent' => $this->percent,
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet]);

        return $dataProvider;
    }
}
