<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RewardsByValidator;
use yii\db\Expression;

/**
 * RewardsByValidatorSearch represents the model behind the search form of `app\models\RewardsByValidator`.
 */
class RewardsByValidatorSearch extends RewardsByValidator
{
    public $validatorName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['validator', 'last_award', 'validatorName','denom'], 'safe'],
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
        $query = RewardsByValidator::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'value' => $this->value,
            'last_award' => $this->last_award,
            'rewards_count' => $this->rewards_count,
            'denom' => $this->denom,
        ]);

        $query->andFilterWhere(['ilike', 'validator', $this->validator])
            ->andFilterWhere(['ilike', 'v.name', $this->validatorName]);


        return $dataProvider;
    }
}
