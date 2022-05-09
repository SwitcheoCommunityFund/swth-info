<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "summary".
 *
 * @property string|null $wallet
 * @property float|null $wait_unbonding_value
 * @property float|null $unbonding_value
 * @property float|null $rewards_value
 * @property string|null $external_in
 * @property string|null $external_out
 */
class Summary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'summary';
    }

    public static function primaryKey()
    {
        return ['wallet'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wait_unbonding_value', 'unbonding_value','staked_value'], 'number'],
            [['external_in', 'external_out','rewards_value'], 'safe'],
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
            'staked_value' => Yii::t('app', 'Staked Amount'),
            'wait_unbonding_value' => Yii::t('app', 'Unstaking Amount'),
            'unbonding_value' => Yii::t('app', 'Unstaked Amount'),
            'rewards_value' => Yii::t('app', 'Rewards Amount'),
            'external_in' => Yii::t('app', 'External In'),
            'external_out' => Yii::t('app', 'External Out'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return SummaryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SummaryQuery(get_called_class());
    }
}
