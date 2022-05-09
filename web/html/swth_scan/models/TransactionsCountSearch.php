<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TransactionsCount;
use kartik\daterange\DateRangeBehavior;
use yii\db\Expression;
use yii\db\conditions\OrCondition;

/**
 * TransactionsCountSearch represents the model behind the search form of `app\models\TransactionsCount`.
 */
class TransactionsCountSearch extends TransactionsCount
{

    public $timeRange;
    public $timeStart;
    public $timeEnd;



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
            [['id', 'count'], 'integer'],
            [['date', 'tr_type'], 'safe'],
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
    public function search($params)
    {
        $session = \Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $query = TransactionsCount::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
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
            'count' => $this->count,
            'date' => $this->date,
            'tr_type'=> $this->tr_type
        ]);

        if($this->timeStart && $this->timeEnd)
        {
            $query->andFilterWhere(['>=', "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->timeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->timeEnd)]);
        }

        return $dataProvider;
    }
}
