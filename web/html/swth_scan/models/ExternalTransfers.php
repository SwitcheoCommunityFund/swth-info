<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "external_transfers".
 *
 * @property int $id
 * @property string|null $wallet
 * @property float|null $amount
 * @property string|null $blockchain
 * @property string|null $denom
 * @property float|null $fee_amount
 * @property string|null $status
 * @property string|null $timestamp
 * @property string|null $transaction_hash
 * @property string|null $transfer_type
 */
class ExternalTransfers extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_transfers';
    }

    public static function primaryKey()
    {
        return ['wallet','transaction_hash'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id','count'], 'integer'],
            [['amount', 'fee_amount'], 'number'],
            [['blockchain', 'denom', 'transfer_type'], 'string'],
            [['timestamp'], 'safe'],
            [['wallet'], 'string', 'max' => 64],
            [['status'], 'string', 'max' => 20],
            [['transaction_hash'], 'string', 'max' => 66],
            [['wallet', 'transaction_hash'], 'unique', 'targetAttribute' => ['wallet', 'transaction_hash']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'wallet' => Yii::t('app', 'Wallet'),
            'amount' => Yii::t('app', 'Amount'),
            'blockchain' => Yii::t('app', 'Blockchain'),
            'denom' => Yii::t('app', 'Denom'),
            'fee_amount' => Yii::t('app', 'Fee Amount'),
            'status' => Yii::t('app', 'Status'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'transaction_hash' => Yii::t('app', 'Transaction Hash'),
            'transfer_type' => Yii::t('app', 'Transfer Type'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ExternalTransfersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExternalTransfersQuery(get_called_class());
    }
}
