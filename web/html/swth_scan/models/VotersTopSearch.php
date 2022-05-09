<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\VotersTop;
use yii\db\Expression;

/**
 * VotersTopSearch represents the model behind the search form of `app\models\VotersTop`.
 */
class VotersTopSearch extends VotersTop
{
    public $voterType = 'validator';


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['votes', 'proposals'], 'integer'],
            [['voter', 'voterType'], 'safe'],
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
        $query = VotersTop::find();

        $query->alias('vo');

        $query->joinWith('v v');

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
            'votes' => $this->votes,
            'proposals' => $this->proposals,
        ]);

        $query->andFilterWhere(['ilike', 'voter', $this->voter]);

        $null = new Expression('null');

        if($this->voterType=='validator'){
            $query->andFilterWhere(['IS NOT','v.name',$null]);
        } elseif($this->voterType=='wallet') {
            $query->andFilterWhere(['IS','v.name',$null]);
        }

        return $dataProvider;
    }
}
