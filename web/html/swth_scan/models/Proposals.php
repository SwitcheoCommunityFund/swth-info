<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proposals".
 *
 * @property int|null $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $proposer
 * @property int|null $proposal_id
 * @property string|null $proposal_type
 * @property string|null $date
 * @property string $tr_hash
 */
class Proposals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proposals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'proposal_id'], 'default', 'value' => null],
            [['id', 'proposal_id'], 'integer'],
            [['description'], 'string'],
            [['date'], 'safe'],
            [['tr_hash'], 'required'],
            [['title'], 'string', 'max' => 255],
            [['proposer', 'tr_hash'], 'string', 'max' => 64],
            [['proposal_type'], 'string', 'max' => 100],
            [['id'], 'unique'],
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
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'proposer' => Yii::t('app', 'Proposer'),
            'proposal_id' => Yii::t('app', 'Proposal ID'),
            'proposal_type' => Yii::t('app', 'Proposal Type'),
            'date' => Yii::t('app', 'Date'),
            'tr_hash' => Yii::t('app', 'Tr Hash'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ProposalsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProposalsQuery(get_called_class());
    }
}
