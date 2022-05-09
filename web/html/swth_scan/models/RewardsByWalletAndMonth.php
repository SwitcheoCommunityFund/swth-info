<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rewards_by_wallet_and_month".
 *
 * @property string|null $wallet
 * @property float|null $value
 * @property string|null $month
 * @property int|null $rewards_count
 */
class RewardsByWalletAndMonth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rewards_by_wallet_and_month';
    }

    public static function primaryKey()
    {
        return ['wallet','month','denom'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['month'], 'safe'],
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
            'month' => Yii::t('app', 'Month'),
            'rewards_count' => Yii::t('app', 'Rewards Count'),
        ];
    }

    public function getToken()
    {
        return $this->hasOne(Tokens::className(), ['denom'=>'denom']);
    }

    /**
     * {@inheritdoc}
     * @return RewardsByWalletAndMonthQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RewardsByWalletAndMonthQuery(get_called_class());
    }
}
