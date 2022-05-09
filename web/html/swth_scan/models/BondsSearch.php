<?php

namespace app\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\Bonds;
use kartik\daterange\DateRangeBehavior;

/**
 * BondsSearch represents the model behind the search form of `app\models\Bonds`.
 */
class BondsSearch extends Bonds
{

    public $varState='waiting';

    public $startTimeRange;
    public $startTimeStart;
    public $startTimeEnd;

    public $endTimeRange;
    public $endTimeStart;
    public $endTimeEnd;

    public $stake_start;
    public $stake_end;

    public $time_to_unstake;

    public $useStakingLapse=false;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'startTimeRange',
                'dateStartAttribute' => 'startTimeStart',
                'dateEndAttribute' => 'startTimeEnd',
            ],
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'endTimeRange',
                'dateStartAttribute' => 'endTimeStart',
                'dateEndAttribute' => 'endTimeEnd',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'value'], 'integer'],
            [['wallet', 'date', 'denom'], 'safe'],
            [['varState','time_to_unstake'], 'safe'],
            [['startTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['endTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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
        $query = Bonds::find();

        if($this->useStakingLapse){
            $query->alias('b');
            $query->leftJoin(['lps' => 'stake_to_unstake'],'b.tr_hash = lps.tr_hash');
            $query->select(['lps.stake_start','lps.stake_end','b.*']);
        }

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
                'defaultOrder' => ['date' => SORT_ASC],
            ],
        ]);

        $dataProvider->sort->attributes['time_to_unstake'] = [
            'asc' => [new Expression("lps.stake_end-lps.stake_start ASC"), 'id' => SORT_ASC],
            'desc' => [new Expression("lps.stake_end-lps.stake_start DESC"), 'id' => SORT_DESC],
            //'label'=>'time_to_unstake'
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
            'value' => $this->value,
            'date' => $this->date,
        ]);


        $now_minus_30_days = new Expression("timezone('UTC',current_timestamp-interval '30 days')");
        if(!empty($this->varState)){
            if($this->varState=='unstaked'){
                $query->andFilterWhere(['<', "date", $now_minus_30_days]);
            }elseif($this->varState=='waiting'){
                $query->andFilterWhere(['>', "date", $now_minus_30_days]);
            }
        }

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet])
              ->andFilterWhere(['ilike', 'denom',  $this->denom]);


        ///////date start ranger with timezone

        if($this->startTimeStart && $this->startTimeEnd)
        {
            /*$startTimeStart = (new \DateTime(date('Y-m-d H:i:s',$this->startTimeStart), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')));
            $startTimeEnd = (new \DateTime(date('Y-m-d H:i:s',$this->startTimeEnd), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')));*/

            //throw new \Exception($startTimeStart->format('Y-m-d H:i:s'));

            $query->andFilterWhere(['>=', "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->startTimeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->startTimeEnd)]);
        }

        ///////date end ranger with timezone

        if($this->endTimeStart && $this->endTimeEnd)
        {
            /*$endTimeStart = (new \DateTime(date('Y-m-d H:i:s',$this->endTimeStart), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')))
                ->modify('- 30days');
            $endTimeEnd = (new \DateTime(date('Y-m-d H:i:s'  ,$this->endTimeEnd), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')))
                ->modify('- 30days');*/

            //throw new \Exception($startTimeStart->format('Y-m-d H:i:s'));

            $query->andFilterWhere(['>=', "timezone('{$timezone}',date) + interval '30 days'", date('Y-m-d H:i:s.uP',$this->endTimeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',date) + interval '30 days'", date('Y-m-d H:i:s.uP',$this->endTimeEnd)]);
        }


        return $dataProvider;
    }
}
