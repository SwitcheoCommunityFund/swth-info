<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Unjails;
use kartik\daterange\DateRangeBehavior;

/**
 * UnjailsSearch represents the model behind the search form of `app\models\Unjails`.
 */
class UnjailsSearch extends Unjails
{

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
            [['id'], 'integer'],
            [['validator','validatorName', 'wallet', 'date'], 'safe'],
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
        $query = Unjails::find();

        $session = \Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $query->alias('u');
        $query->joinWith('v v');

        $query->select(['u.*','v.name as validatorName']);

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
        ]);

        $query->andFilterWhere(['ilike', 'wallet', $this->wallet])
              ->andFilterWhere(['ilike', 'v.name', $this->validatorName]);


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

        return $dataProvider;
    }
}
