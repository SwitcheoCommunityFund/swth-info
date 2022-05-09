<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Votes;
use yii\db\Expression;

/**
 * VotesSearch represents the model behind the search form of `app\models\Votes`.
 */
class VotesSearch extends Votes
{
    public $voterType = 'validator';
    public $proposalTitle = null;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'proposal_id'], 'integer'],
            [['tr_hash', 'voter', 'option', 'date','voterType','proposalTitle'], 'safe'],
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
        $query = Votes::find();

        $query->alias('vo');

        $query->joinWith('v v');

        $query->joinWith('p pr');



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort'=>[
                'defaultOrder' => ['date' => SORT_DESC]
            ],
        ]);

        $this->load($params);


        if($this->proposal_id===null){
            $this->proposal_id = Votes::find()->max('proposal_id');
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'vo.id' => $this->id,
            'vo.proposal_id' => $this->proposal_id,
            'vo.date' => $this->date,
        ]);

        $query->andFilterWhere(['ilike', 'vo.tr_hash', $this->tr_hash])
            ->andFilterWhere(['or',['ilike', 'voter', $this->voter],['ilike', 'v.name', $this->voter]])
            ->andFilterWhere(['ilike', 'pr.title', $this->proposalTitle])
            ->andFilterWhere(['ilike', 'option', $this->option]);

        $null = new Expression('null');

        if($this->voterType=='validator'){
            $query->andFilterWhere(['IS NOT','v.name',$null]);
        } elseif($this->voterType=='wallet') {
            $query->andFilterWhere(['IS','v.name',$null]);
        }

        return $dataProvider;
    }
}
