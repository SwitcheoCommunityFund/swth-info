<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AirdropLog;

/**
 * AirdropLogSearch represents the model behind the search form of `app\models\AirdropLog`.
 */
class AirdropLogSearch extends AirdropLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['tx_id', 'air_time', 'wallet', 'log'], 'safe'],
            [['amount'], 'number'],
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
        $query = AirdropLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=>[
                'defaultOrder' => ['air_time' => SORT_DESC]
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
            'id' => $this->id,
            'air_time' => $this->air_time,
            'amount' => $this->amount,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'tx_id', $this->tx_id])
            ->andFilterWhere(['ilike', 'wallet', $this->wallet])
            ->andFilterWhere(['ilike', 'log', $this->log]);

        return $dataProvider;
    }
}
