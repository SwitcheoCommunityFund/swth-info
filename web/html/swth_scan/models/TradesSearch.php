<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Trades;
use kartik\daterange\DateRangeBehavior;
use yii\db\Expression;
use yii\db\conditions\OrCondition;

/**
 * TradesSearch represents the model behind the search form of `app\models\Trades`.
 */
class TradesSearch extends Trades
{

    public $varPreciseValues=true;

    public $timeRange;
    public $timeStart;
    public $timeEnd;

    public $priceQuote;
    public $usdPrice;
    public $amountUsd;
    public $amount;

    public $wallet;


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
    public function rules()
    {
        return [
            [['id', 'block_height'], 'integer'],
            [['block_created_at', 'taker_id', 'taker_address', 'taker_fee_denom', 'taker_side', 'maker_id', 'maker_address', 'maker_fee_denom', 'maker_side', 'market', 'liquidation'
                ,'amount','priceQuote','varPreciseValues','usdPrice','amountUsd'
                ,'wallet'], 'safe'],
            [['taker_fee_amount', 'maker_fee_amount', 'price', 'quantity'], 'number'],
            [['timeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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
    public function search($params,$asQuery=false)
    {
        $query = Trades::find();

        $session = \Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;


        $query->alias('tr');

        $query->joinWith('m m');

        $query->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)');

        $query->leftJoin(['th' => 'token_history'],"currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)");

        $query->select(['tr.*','m.*','th.current_price as usdPrice']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=>[
                'defaultOrder' => ['block_created_at' => SORT_DESC]
            ],
        ]);

        $dataProvider->sort->attributes['amountUsd']=[
            'asc'=>new Expression('tr.price * tr.quantity * th.current_price ASC'),
            'desc'=>new Expression('tr.price * tr.quantity * th.current_price DESC'),
        ];

        $dataProvider->sort->attributes['amount']=[
            'asc'=>new Expression('tr.price * tr.quantity ASC'),
            'desc'=>new Expression('tr.price * tr.quantity DESC'),
        ];

        $this->load($params);



        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'block_created_at' => $this->block_created_at,
            'taker_fee_amount' => $this->taker_fee_amount,
            'maker_fee_amount' => $this->maker_fee_amount,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'block_height' => $this->block_height,
            'm.quote'=> $this->priceQuote
        ]);

        $query->andFilterWhere(['ilike', 'taker_id', $this->taker_id])
            ->andFilterWhere(['ilike', 'taker_address', $this->taker_address])
            ->andFilterWhere(['ilike', 'taker_fee_denom', $this->taker_fee_denom])
            ->andFilterWhere(['ilike', 'taker_side', $this->taker_side])
            ->andFilterWhere(['ilike', 'maker_id', $this->maker_id])
            ->andFilterWhere(['ilike', 'maker_address', $this->maker_address])
            ->andFilterWhere(['ilike', 'maker_fee_denom', $this->maker_fee_denom])
            ->andFilterWhere(['ilike', 'maker_side', $this->maker_side])
            ->andFilterWhere(['ilike', 'market', $this->market])
            ->andFilterWhere(['ilike', 'liquidation', $this->liquidation]);

        if(!empty($this->wallet))
        $query->andWhere(new OrCondition([['ilike', 'taker_address', $this->wallet],['ilike', 'maker_address', $this->wallet]]));

        if($this->timeStart && $this->timeEnd)
        {
            $query->andFilterWhere(['>=', "timezone('{$timezone}',block_created_at)", date('Y-m-d H:i:s.uP',$this->timeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',block_created_at)", date('Y-m-d H:i:s.uP',$this->timeEnd)]);
        }

        if($asQuery) return $query;

        return $dataProvider;
    }
}
