<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rewards_by_wallet_and_validator".
 *
 * @property string|null $wallet
 * @property string|null $validator
 * @property float|null $value
 * @property string|null $last_award
 * @property int|null $rewards_count
 */
class RewardsByWalletAndValidator extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rewards_by_wallet_and_validator';
    }

    public static function primaryKey()
    {
        return ['wallet','validator','denom'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['last_award'], 'safe'],
            [['rewards_count'], 'default', 'value' => null],
            [['rewards_count'], 'integer'],
            [['wallet', 'validator'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'wallet' => Yii::t('app', 'Wallet'),
            'validator' => Yii::t('app', 'Validator'),
            'value' => Yii::t('app', 'Value'),
            'last_award' => Yii::t('app', 'Last Award'),
            'rewards_count' => Yii::t('app', 'Rewards Count'),
        ];
    }

    public function getV()
    {
        return $this->hasOne(Validators::className(), ['address'=>'validator']);
    }

    public function getToken()
    {
        return $this->hasOne(Tokens::className(), ['denom'=>'denom']);
    }

    /**
     * {@inheritdoc}
     * @return RewardsByWalletAndValidatorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RewardsByWalletAndValidatorQuery(get_called_class());
    }
}
