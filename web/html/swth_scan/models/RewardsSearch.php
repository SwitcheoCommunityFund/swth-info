<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Rewards;
use kartik\daterange\DateRangeBehavior;
use yii\db\Expression;

/**
 * RewardsSearch represents the model behind the search form of `app\models\Rewards`.
 */
class RewardsSearch extends Rewards
{
    public $varEmptyValues=false;
    public $varPreciseValues=false;

    public $timeRange;
    public $timeStart;
    public $timeEnd;

    public $validatorName;

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
            [['id', 'value'], 'integer'],
            [['wallet', 'validator', 'date', 'denom','tr_type',
              'varEmptyValues','varPreciseValues','validatorName'], 'safe'],
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
     * @param bool $asQuery=false
     *
     * @return ActiveDataProvider
     */
    public function search($params,$asQuery=false)
    {

        $session = \Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $query = Rewards::find();
        $query->alias('r');
        $query->joinWith('v v');
        $query->joinWith('token t');
        $query->select(['r.*','v.name as validatorName']);

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

        $dataProvider->sort->attributes['validatorName']=[
            'asc'=>['v.name'=>SORT_ASC],
            'desc'=>['v.name'=>SORT_DESC],
        ];
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

        // grid filtering conditions
        $query->andFilterWhere([
            'r.id' => $this->id,
            'date' => $this->date,
            'value' => $this->value,
            'tr_type' => $this->tr_type,
        ]);

        $query->andFilterWhere(['ilike', 'r.wallet', $this->wallet])
            ->andFilterWhere(['ilike', 'validator', $this->validator])
            ->andFilterWhere(['ilike', 'v.name', $this->validatorName]);
            //->andFilterWhere(['=', 'r.denom', $this->denom]);


        $null = new Expression('NULL');

        if($this->denom==='null'){
            $query->andFilterWhere(['is','r.denom',$null]);
        } else $query->andFilterWhere(['r.denom'=>$this->denom]);

        if(!$this->varEmptyValues)
            $query->andWhere('value is not null');

        ///////date start ranger with timezone

        if($this->timeStart && $this->timeEnd)
        {
            /*$timeStart = (new \DateTime(date('Y-m-d H:i:s',$this->timeStart), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')));
            $timeEnd = (new \DateTime(date('Y-m-d H:i:s',$this->timeEnd), (new \DateTimeZone($timezone))))
                ->setTimezone((new \DateTimeZone('UTC')));*/

            //throw new \Exception($startTimeStart->format('Y-m-d H:i:s'));

            $query->andFilterWhere(['>=', "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->timeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',date)", date('Y-m-d H:i:s.uP',$this->timeEnd)]);
        }

        //throw new \Exception($query->createCommand()->getRawSql());

        if($asQuery) return $query;

        return $dataProvider;
    }
}
