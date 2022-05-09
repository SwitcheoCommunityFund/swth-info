<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "markets".
 *
 * @property string|null $type
 * @property string $name
 * @property string|null $display_name
 * @property string|null $description
 * @property string|null $market_type
 * @property string|null $base
 * @property string|null $base_name
 * @property int|null $base_precision
 * @property string|null $quote
 * @property string|null $quote_name
 * @property int|null $quote_precision
 * @property float|null $lot_size
 * @property float|null $tick_size
 * @property float|null $min_quantity
 * @property float|null $maker_fee
 * @property float|null $taker_fee
 * @property int|null $risk_step_size
 * @property int|null $initial_margin_base
 * @property int|null $initial_margin_step
 * @property int|null $maintenance_margin_ratio
 * @property int|null $max_liquidation_order_ticket
 * @property int|null $max_liquidation_order_duration
 * @property int|null $impact_size
 * @property int|null $mark_price_band
 * @property int|null $last_price_protected_band
 * @property int|null $index_oracle_id
 * @property string|null $expiry_time
 * @property bool|null $is_active
 * @property bool|null $is_settled
 * @property int|null $closed_block_height
 * @property int|null $created_block_height
 */
class Markets extends \yii\db\ActiveRecord
{

    public $monthCountA;
    public $monthCountB;
    public $monthSumA;
    public $monthSumB;

    public $weekCountA;
    public $weekCountB;
    public $weekSumA;
    public $weekSumB;

    public $h24CountA;
    public $h24CountB;
    public $h24SumA;
    public $h24SumB;

    public $countA;
    public $countB;
    public $sumA;
    public $sumB;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'markets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['base_precision', 'quote_precision', 'risk_step_size', 'initial_margin_base', 'initial_margin_step', 'maintenance_margin_ratio', 'max_liquidation_order_ticket', 'max_liquidation_order_duration', 'impact_size', 'mark_price_band', 'last_price_protected_band', 'index_oracle_id', 'closed_block_height', 'created_block_height'], 'default', 'value' => null],
            [['base_precision', 'quote_precision', 'risk_step_size', 'initial_margin_base', 'initial_margin_step', 'maintenance_margin_ratio', 'max_liquidation_order_ticket', 'max_liquidation_order_duration', 'impact_size', 'mark_price_band', 'last_price_protected_band', 'index_oracle_id', 'closed_block_height', 'created_block_height'], 'integer'],
            [['lot_size', 'tick_size', 'min_quantity', 'maker_fee', 'taker_fee'], 'number'],
            [['expiry_time'], 'safe'],
            [['is_active', 'is_settled'], 'boolean'],
            [['type', 'market_type', 'base', 'base_name', 'quote', 'quote_name'], 'string', 'max' => 50],
            [['name', 'display_name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'display_name' => Yii::t('app', 'Display Name'),
            'description' => Yii::t('app', 'Description'),
            'market_type' => Yii::t('app', 'Market Type'),
            'base' => Yii::t('app', 'Base'),
            'base_name' => Yii::t('app', 'Base Name'),
            'base_precision' => Yii::t('app', 'Base Precision'),
            'quote' => Yii::t('app', 'Quote'),
            'quote_name' => Yii::t('app', 'Quote Name'),
            'quote_precision' => Yii::t('app', 'Quote Precision'),
            'lot_size' => Yii::t('app', 'Lot Size'),
            'tick_size' => Yii::t('app', 'Tick Size'),
            'min_quantity' => Yii::t('app', 'Min Quantity'),
            'maker_fee' => Yii::t('app', 'Maker Fee'),
            'taker_fee' => Yii::t('app', 'Taker Fee'),
            'risk_step_size' => Yii::t('app', 'Risk Step Size'),
            'initial_margin_base' => Yii::t('app', 'Initial Margin Base'),
            'initial_margin_step' => Yii::t('app', 'Initial Margin Step'),
            'maintenance_margin_ratio' => Yii::t('app', 'Maintenance Margin Ratio'),
            'max_liquidation_order_ticket' => Yii::t('app', 'Max Liquidation Order Ticket'),
            'max_liquidation_order_duration' => Yii::t('app', 'Max Liquidation Order Duration'),
            'impact_size' => Yii::t('app', 'Impact Size'),
            'mark_price_band' => Yii::t('app', 'Mark Price Band'),
            'last_price_protected_band' => Yii::t('app', 'Last Price Protected Band'),
            'index_oracle_id' => Yii::t('app', 'Index Oracle ID'),
            'expiry_time' => Yii::t('app', 'Expiry Time'),
            'is_active' => Yii::t('app', 'Is Active'),
            'is_settled' => Yii::t('app', 'Is Settled'),
            'closed_block_height' => Yii::t('app', 'Closed Block Height'),
            'created_block_height' => Yii::t('app', 'Created Block Height'),
        ];
    }


    /**
     * {@inheritdoc}
     * @return MarketsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MarketsQuery(get_called_class());
    }
}
