<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\GroupedBonds;

/**
 * GroupedBondsSearch represents the model behind the search form of `app\models\GroupedBonds`.
 */
class GroupedBondsSearch extends GroupedBonds
{
    public $varValueAll;
    public $varEmptyValues;
    public $varPreciseValues;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet', 'first_date', 'last_date','varValueAll','varEmptyValues','varPreciseValues'], 'safe'],
            [['value'], 'number'],
            [['bonds_count'], 'integer'],
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
        $query = GroupedBonds::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder' => ['value' => SORT_DESC],
                'attributes'=>[
                    'id',
                    'wallet',
                    'first_date',
                    'last_date',
                    'value',
                    'bonds_count',
                    'varValueAll'=>[
                        'asc'=>['"value"+"unbonded_value"'=>SORT_ASC],
                        'desc'=>['"value"+"unbonded_value"'=>SORT_DESC],
                    ]
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if(!$this->varEmptyValues)
            $query->andWhere('value is not null');

        // grid filtering conditions
        $query->andFilterWhere([
            'value' => $this->value,
            'first_date' => $this->first_date,
            'last_date' => $this->last_date,
            'bonds_count' => $this->bonds_count,
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet]);

        return $dataProvider;
    }
}
