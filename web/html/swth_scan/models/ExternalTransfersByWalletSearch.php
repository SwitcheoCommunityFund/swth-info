<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ExternalTransfersByWallet;

/**
 * ExternalTransfersByWalletSearch represents the model behind the search form of `app\models\ExternalTransfersByWallet`.
 */
class ExternalTransfersByWalletSearch extends ExternalTransfersByWallet
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet', 'denom', 'last_out', 'last_in','count'], 'safe'],
            [['out', 'in', 'balance'], 'number'],
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
        $query = ExternalTransfersByWallet::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['in']=[
            'asc'=>['coalesce("in",0)'=>SORT_ASC],
            'desc'=>['coalesce("in",0)'=>SORT_DESC],
        ];

        $dataProvider->sort->attributes['out']=[
            'asc'=>['coalesce("out",0)'=>SORT_ASC],
            'desc'=>['coalesce("out",0)'=>SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'out' => $this->out,
            'in' => $this->in,
            'last_out' => $this->last_out,
            'last_in' => $this->last_in,
            'balance' => $this->balance,
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet])
            ->andFilterWhere(['denom'=>$this->denom]);

        return $dataProvider;
    }
}
