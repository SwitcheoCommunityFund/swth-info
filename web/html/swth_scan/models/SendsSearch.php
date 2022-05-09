<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Sends;
use kartik\daterange\DateRangeBehavior;
use yii\db\Expression;
use yii\db\conditions\OrCondition;

/**
 * SendsSearch represents the model behind the search form of `app\models\Sends`.
 */
class SendsSearch extends Sends
{

    public $timeRange;
    public $timeStart;
    public $timeEnd;

    public $wallet;

    public $varPreciseValues = false;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['tr_hash', 'from', 'to', 'date', 'denom','wallet','varPreciseValues'], 'safe'],
            [['amount'], 'number'],
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
        $query = Sends::find();

        $session = \Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort'=>[
                'defaultOrder' => ['date' => SORT_DESC]
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
            'date' => $this->date,
            'amount' => $this->amount,
            'denom' => $this->denom,
        ]);

        $query->andFilterWhere(['ilike', 'tr_hash', $this->tr_hash])
              ->andFilterWhere(['ilike', 'from', $this->from])
              ->andFilterWhere(['ilike', 'to', $this->to]);


        if(!empty($this->wallet))
            $query->andWhere(new OrCondition([['ilike', 'from', $this->wallet],['ilike', 'to', $this->wallet]]));

        if($this->timeStart && $this->timeEnd)
        {
            $query->andFilterWhere(['>=', "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->timeStart)])
                  ->andFilterWhere(['<',  "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->timeEnd)]);
        }

        return $dataProvider;
    }
}
