<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "token_pairs".
 *
 * @property string $id
 * @property string $system
 * @property float|null $reserve_usd
 * @property string|null $token0_symbol
 * @property string|null $token0_id
 * @property float|null $token0_price
 * @property int|null $token0_decimals
 * @property float|null $reserve0
 * @property string|null $token1_symbol
 * @property string|null $token1_id
 * @property float|null $token1_price
 * @property int|null $token1_decimals
 * @property float|null $reserve1
 * @property string|null $updated
 */
class TokenPairs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'token_pairs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'system'], 'required'],
            [['reserve_usd', 'token0_price', 'reserve0', 'token1_price', 'reserve1'], 'number'],
            [['token0_decimals', 'token1_decimals'], 'default', 'value' => null],
            [['token0_decimals', 'token1_decimals'], 'integer'],
            [['updated'], 'safe'],
            [['id', 'token0_id', 'token1_id'], 'string', 'max' => 70],
            [['system'], 'string', 'max' => 20],
            [['token0_symbol', 'token1_symbol'], 'string', 'max' => 100],
            [['id', 'system'], 'unique', 'targetAttribute' => ['id', 'system']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'system' => Yii::t('app', 'System'),
            'reserve_usd' => Yii::t('app', 'Reserve Usd'),
            'token0_symbol' => Yii::t('app', 'Token0 Symbol'),
            'token0_id' => Yii::t('app', 'Token0 ID'),
            'token0_price' => Yii::t('app', 'Token0 Price'),
            'token0_decimals' => Yii::t('app', 'Token0 Decimals'),
            'reserve0' => Yii::t('app', 'Reserve0'),
            'token1_symbol' => Yii::t('app', 'Token1 Symbol'),
            'token1_id' => Yii::t('app', 'Token1 ID'),
            'token1_price' => Yii::t('app', 'Token1 Price'),
            'token1_decimals' => Yii::t('app', 'Token1 Decimals'),
            'reserve1' => Yii::t('app', 'Reserve1'),
            'updated' => Yii::t('app', 'Updated'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TokenPairsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TokenPairsQuery(get_called_class());
    }
}
