<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RewardsByWalletAndValidator;

/**
 * RewardsByWalletAndValidatorSearch represents the model behind the search form of `app\models\RewardsByWalletAndValidator`.
 */
class RewardsByWalletAndValidatorSearch extends RewardsByWalletAndValidator
{
    public $validatorName;
    public $varEmptyValues;
    public $varPreciseValues;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet', 'validator', 'last_award', 'validatorName','varEmptyValues','varPreciseValues','denom'], 'safe'],
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
        $query = RewardsByWalletAndValidator::find();

        $query->alias('r');

        $query->joinWith('v v');

        $query->select(['r.*','v.name as validatorName']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['value' => SORT_DESC]],
        ]);


        $dataProvider->sort->attributes['validatorName']=[
            'asc'=>['v.name'=>SORT_ASC],
            'desc'=>['v.name'=>SORT_DESC],
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

        $query->andFilterWhere(['ilike', 'r.wallet', $this->wallet])
            ->andFilterWhere(['ilike', 'validator', $this->validator])
            ->andFilterWhere(['ilike', 'v.name', $this->validatorName]);

        return $dataProvider;
    }
}
