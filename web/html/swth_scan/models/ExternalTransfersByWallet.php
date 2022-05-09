<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "external_transfers_by_wallet".
 *
 * @property string|null $wallet
 * @property string|null $blockchain
 * @property float|null $out
 * @property float|null $in
 * @property string|null $last_out
 * @property string|null $last_in
 * @property float|null $balance
 */
class ExternalTransfersByWallet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'external_transfers_by_wallet';
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
            [['denom'], 'string'],
            [['out', 'in', 'balance'], 'number'],
            [['last_out', 'last_in'], 'safe'],
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
            'denom' => Yii::t('app', 'Denom'),
            'out' => Yii::t('app', 'Out'),
            'in' => Yii::t('app', 'In'),
            'last_out' => Yii::t('app', 'Last Out'),
            'last_in' => Yii::t('app', 'Last In'),
            'balance' => Yii::t('app', 'Balance'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ExternalTransfersByWalletQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExternalTransfersByWalletQuery(get_called_class());
    }
}
