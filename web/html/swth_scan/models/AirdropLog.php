<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "airdrop_log".
 *
 * @property int $id
 * @property string|null $tx_id
 * @property string|null $air_time
 * @property float|null $amount
 * @property string|null $wallet
 * @property int|null $status
 * @property string|null $log
 *
 * @property AirdropLogStates $status0
 */
class AirdropLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'airdrop_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['air_time'], 'safe'],
            [['amount'], 'number'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['log'], 'string'],
            [['tx_id'], 'string', 'max' => 66],
            [['wallet'], 'string', 'max' => 64],
            [['tx_id'], 'unique'],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => AirdropLogStates::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tx_id' => Yii::t('app', 'Tx ID'),
            'air_time' => Yii::t('app', 'Air Time'),
            'amount' => Yii::t('app', 'Amount'),
            'wallet' => Yii::t('app', 'Wallet'),
            'status' => Yii::t('app', 'Status'),
            'log' => Yii::t('app', 'Log'),
        ];
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery|AirdropLogStatesQuery
     */
    public function getStates()
    {
        return $this->hasOne(AirdropLogStates::className(), ['id' => 'status']);
    }

    /**
     * {@inheritdoc}
     * @return AirdropLogQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AirdropLogQuery(get_called_class());
    }
}
