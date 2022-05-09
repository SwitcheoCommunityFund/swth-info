<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RewardsByWallet;

/**
 * RewardsByWalletSearch represents the model behind the search form of `app\models\RewardsByWallet`.
 */
class RewardsByWalletSearch extends RewardsByWallet
{
    public $varEmptyValues=false;
    public $varPreciseValues=false;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet', 'last_award', 'varEmptyValues', 'varPreciseValues','denom'], 'safe'],
            [['value'], 'number'],
            [['rewards_count'], 'integer'],
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
        $query = RewardsByWallet::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['value' => SORT_DESC]],
        ]);

        $dataProvider->sort->attributes['value']=[
            'asc'=>['coalesce("value",0)'=>SORT_ASC],
            'desc'=>['coalesce("value",0)'=>SORT_DESC],
        ];

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
            'last_award' => $this->last_award,
            'rewards_count' => $this->rewards_count,
            'denom' => $this->denom,
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet]);

        return $dataProvider;
    }
}
