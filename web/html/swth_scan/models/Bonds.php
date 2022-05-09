<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bonds".
 *
 * @property int|null $id
 * @property string|null $wallet
 * @property int|null $value
 * @property string|null $date
 * @property string|null $denom
 */
class Bonds extends \yii\db\ActiveRecord
{

    public $stake_start;
    public $stake_end;
    public $time_to_unstake;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bonds';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'value'], 'default', 'value' => null],
            [['id', 'value'], 'integer'],
            [['date'], 'safe'],
            [['denom'], 'string'],
            [['wallet'], 'string', 'max' => 64],
            [['id'], 'unique'],
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
            'value' => Yii::t('app', 'Amount'),
            'date' => Yii::t('app', 'Date'),
            'denom' => Yii::t('app', 'Denom'),
        ];
    }

    public function getToken()
    {
        return $this->hasOne(Tokens::className(), ['denom'=>'denom']);
    }

    /**
     * {@inheritdoc}
     * @return BondsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BondsQuery(get_called_class());
    }
}
