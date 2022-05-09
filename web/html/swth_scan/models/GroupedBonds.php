<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "grouped_bonds".
 *
 * @property string|null $wallet
 * @property float|null $value
 * @property string|null $first_date
 * @property string|null $last_date
 * @property int|null $bonds_count
 */
class GroupedBonds extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grouped_bonds_v1';
    }

    public static function primaryKey(){

        return ['wallet'];

    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['first_date', 'last_date', 'value'], 'safe'],
            [['bonds_count'], 'default', 'value' => null],
            [['bonds_count','unbonded_count'], 'integer'],
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
            'value' => Yii::t('app', 'Amount wait'),
            'first_date' => Yii::t('app', 'Closest Unstaking Ends'),
            'last_date' => Yii::t('app', 'Last Unstaking Ends'),
            'bonds_count' => Yii::t('app', 'Count Waiting / All'),
            'varValueAll' => Yii::t('app', 'Amount All'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return GroupedBondsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new GroupedBondsQuery(get_called_class());
    }
}
