<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rewards_by_wallet".
 *
 * @property string|null $wallet
 * @property float|null $value
 * @property string|null $last_award
 * @property int|null $rewards_count
 */
class RewardsByWallet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rewards_by_wallet';
    }

    public static function primaryKey()
    {
        return ['wallet','denom'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['last_award','denom'], 'safe'],
            [['rewards_count'], 'default', 'value' => null],
            [['rewards_count'], 'integer'],
            [['wallet'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wallet' => Yii::t('app', 'Wallet'),
            'value' => Yii::t('app', 'Value'),
            'last_award' => Yii::t('app', 'Last Award'),
            'rewards_count' => Yii::t('app', 'Rewards Count'),
        ];
    }


    public function getToken()
    {
        return $this->hasOne(Tokens::className(), ['denom'=>'denom']);
    }

    /**
     * {@inheritdoc}
     * @return RewardsByWalletQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RewardsByWalletQuery(get_called_class());
    }
}
