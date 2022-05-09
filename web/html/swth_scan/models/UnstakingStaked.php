<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unstaking_staked".
 *
 * @property string|null $date
 * @property string|null $wallet
 * @property float|null $value
 * @property string|null $type
 * @property float|null $value_delegated
 * @property int|null $count_delegated
 * @property float|null $percent
 */
class UnstakingStaked extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unstaking_staked';
    }

    public static function primaryKey(){

        return ['date','wallet'];
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['value', 'value_delegated', 'percent', 'delegation_balance'], 'number'],
            [['count_delegated'], 'default', 'value' => null],
            [['count_delegated'], 'integer'],
            [['wallet'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'date' => Yii::t('app', 'Begin Unstaking'),
            'wallet' => Yii::t('app', 'Wallet'),
            'value' => Yii::t('app', 'Unstaking'),
            'value_delegated' => Yii::t('app', 'Delegated'),
            'delegation_balance' => Yii::t('app', 'Staking'),
            'count_delegated' => Yii::t('app', 'Count Delegated'),
            'percent' => Yii::t('app', 'Unstaked (%)'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return UnstakingStakedQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UnstakingStakedQuery(get_called_class());
    }
}
