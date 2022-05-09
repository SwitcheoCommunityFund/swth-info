<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AccountInfo;
use kartik\daterange\DateRangeBehavior;
use yii\db\Expression;

/**
 * AccountInfoSearch represents the model behind the search form of `app\models\AccountInfo`.
 */
class AccountInfoSearch extends AccountInfo
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
            [['id'], 'integer'],
            [['account', 'tr_first', 'tr_hash', 'username'], 'safe'],
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

        $query = AccountInfo::find();

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
        ]);

        $query->andFilterWhere(['ilike', 'account', $this->account])
            ->andFilterWhere(['ilike', 'tr_hash', $this->tr_hash])
            ->andFilterWhere(['ilike', 'username', $this->username]);


        if($this->timeStart && $this->timeEnd)
        {
            $query->andFilterWhere(['>=', "timezone('{$timezone}',tr_first)", date('Y-m-d H:i:s.uP',$this->timeStart)])
                ->andFilterWhere(['<',  "timezone('{$timezone}',tr_first)", date('Y-m-d H:i:s.uP',$this->timeEnd)]);
        }

        return $dataProvider;
    }
}
