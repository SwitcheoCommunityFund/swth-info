<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "voters_top".
 *
 * @property int|null $votes
 * @property int|null $proposals
 * @property string|null $voter
 */
class VotersTop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'voters_top';
    }

    public static function primaryKey()
    {
        return ['voter'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['votes', 'proposals'], 'default', 'value' => null],
            [['votes', 'proposals'], 'integer'],
            [['voter'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'votes' => Yii::t('app', 'Votes'),
            'proposals' => Yii::t('app', 'Proposals'),
            'voter' => Yii::t('app', 'Voter'),
        ];
    }

    public function getV()
    {
        return $this->hasOne(Validators::className(), ['wallet'=>'voter']);
    }

    /**
     * {@inheritdoc}
     * @return VotersTopQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VotersTopQuery(get_called_class());
    }
}
