<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ExternalTransfers;
use kartik\daterange\DateRangeBehavior;

/**
 * ExternalTransfersSearch represents the model behind the search form of `app\models\ExternalTransfers`.
 */
class ExternalTransfersSearch extends ExternalTransfers
{
    public $timeRange;
    public $timeStart;
    public $timeEnd;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['wallet', 'blockchain', 'denom', 'status', 'timestamp', 'transaction_hash', 'transfer_type'], 'safe'],
            [['amount', 'fee_amount'], 'number'],
            [['timeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'timeRange',
                'dateStartAttribute' => 'timeStart',
                'dateEndAttribute' => 'timeEnd',
            ],
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
        $query = ExternalTransfers::find();

        $session = \Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>[
                'defaultOrder' => ['timestamp' => SORT_DESC]
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
            'id' => $this->id,
            'amount' => $this->amount,
            'fee_amount' => $this->fee_amount,
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet])
            ->andFilterWhere(['ilike', 'cast(blockchain as text)', $this->blockchain])
            ->andFilterWhere(['=', 'cast(denom as text)', $this->denom])
            ->andFilterWhere(['ilike', 'status', $this->status])
            ->andFilterWhere(['ilike', 'transaction_hash', $this->transaction_hash])
            ->andFilterWhere(['ilike', 'cast(transfer_type as text)', $this->transfer_type]);


        if($this->timeStart && $this->timeEnd)
        {
            /*$timeStart = (new \DateTime(date('Y-m-d H:i:s',$this->timeStart), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')));
            $timeEnd = (new \DateTime(date('Y-m-d H:i:s'  ,$this->timeEnd), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')));*/

            //throw new \Exception($startTimeStart->format('Y-m-d H:i:s'));

            $query->andFilterWhere(['>=', "timezone('{$timezone}',timestamp)", date('Y-m-d H:i:s.uP',$this->timeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',timestamp)", date('Y-m-d H:i:s.uP',$this->timeEnd)]);


        }

        return $dataProvider;
    }
}
