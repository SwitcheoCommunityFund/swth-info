<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trades".
 *
 * @property int $id
 * @property string|null $block_created_at
 * @property string|null $taker_id
 * @property string|null $taker_address
 * @property float|null $taker_fee_amount
 * @property string|null $taker_fee_denom
 * @property string|null $taker_side
 * @property string|null $maker_id
 * @property string|null $maker_address
 * @property float|null $maker_fee_amount
 * @property string|null $maker_fee_denom
 * @property string|null $maker_side
 * @property string|null $market
 * @property float|null $price
 * @property float|null $quantity
 * @property string|null $liquidation
 * @property int|null $block_height
 */
class Trades extends \yii\db\ActiveRecord
{
    public $trade_sum;
    public $trade_month_sum;
    public $trade_24h_sum;
    public $count;


    public $denom;


    public $usdPrice;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'block_height'], 'default', 'value' => null],
            [['id', 'block_height'], 'integer'],
            [['block_created_at'], 'safe'],
            [['taker_fee_amount', 'maker_fee_amount', 'price', 'quantity'], 'number'],
            [['liquidation'], 'string'],
            [['taker_id', 'taker_address', 'maker_id', 'maker_address'], 'string', 'max' => 64],
            [['taker_fee_denom', 'taker_side', 'maker_fee_denom', 'market'], 'string', 'max' => 50],
            [['maker_side'], 'string', 'max' => 30],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => Yii::t('app', 'ID'),
            'block_created_at' => Yii::t('app', 'Date'),
            'taker_id'         => Yii::t('app', 'Taker ID'),
            'taker_address'    => Yii::t('app', 'Taker Address'),
            'taker_fee_amount' => Yii::t('app', 'Taker Fee Amount'),
            'taker_fee_denom'  => Yii::t('app', 'Taker Fee Denom'),
            'taker_side'       => Yii::t('app', 'Taker Side'),
            'maker_id'         => Yii::t('app', 'Maker ID'),
            'maker_address'    => Yii::t('app', 'Maker Wallet'),
            'maker_fee_amount' => Yii::t('app', 'Fee Amount'),
            'maker_fee_denom'  => Yii::t('app', 'Fee Denom'),
            'maker_side'       => Yii::t('app', 'Trade'),
            'market'           => Yii::t('app', 'Market'),
            'price'            => Yii::t('app', 'Price'),
            'quantity'         => Yii::t('app', 'Quantity'),
            'liquidation'      => Yii::t('app', 'Liquidation'),
            'block_height'     => Yii::t('app', 'Block Height'),
            'wallet'           => Yii::t('app', 'Search by Wallet'),
        ];
    }

    public function getM()
    {
        return $this->hasOne(Markets::className(), ['name'=>'market']);
    }

    /**
     * {@inheritdoc}
     * @return TradesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TradesQuery(get_called_class());
    }
}
