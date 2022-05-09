<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chain_denom_decimal".
 *
 * @property int $id
 * @property string $blockchain
 * @property string|null $denom
 * @property int|null $decimal
 * @property string|null $full_name
 */
class ChainDenomDecimal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chain_denom_decimal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blockchain'], 'required'],
            [['blockchain', 'denom'], 'string'],
            [['decimal'], 'default', 'value' => null],
            [['decimal'], 'integer'],
            [['full_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'blockchain' => Yii::t('app', 'Blockchain'),
            'denom' => Yii::t('app', 'Denom'),
            'decimal' => Yii::t('app', 'Decimal'),
            'full_name' => Yii::t('app', 'Full Name'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ChainDenomDecimalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ChainDenomDecimalQuery(get_called_class());
    }
}
