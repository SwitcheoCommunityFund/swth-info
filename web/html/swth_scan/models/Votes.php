<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "votes".
 *
 * @property int $id
 * @property string $tr_hash
 * @property int|null $proposal_id
 * @property string|null $voter
 * @property string|null $option
 * @property string|null $date
 */
class Votes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'votes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tr_hash'], 'required'],
            [['proposal_id'], 'default', 'value' => null],
            [['proposal_id'], 'integer'],
            [['date'], 'safe'],
            [['tr_hash', 'voter'], 'string', 'max' => 64],
            [['option'], 'string', 'max' => 10],
            [['tr_hash'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tr_hash' => Yii::t('app', 'Tr Hash'),
            'proposal_id' => Yii::t('app', 'Proposal ID'),
            'voter' => Yii::t('app', 'Voter'),
            'option' => Yii::t('app', 'Option'),
            'date' => Yii::t('app', 'Date'),
        ];
    }


    public function getV()
    {
        return $this->hasOne(Validators::className(), ['wallet'=>'voter']);
    }

    public function getP()
    {
        return $this->hasOne(Proposals::className(), ['proposal_id'=>'proposal_id']);
    }

    /**
     * {@inheritdoc}
     * @return VotesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VotesQuery(get_called_class());
    }
}
