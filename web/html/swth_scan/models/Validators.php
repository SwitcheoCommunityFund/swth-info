<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "validators".
 *
 * @property int|null $id
 * @property string $address
 * @property string|null $name
 * @property string|null $details
 * @property string|null $wallet
 */
class Validators extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'validators';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address'], 'required'],
            [['details'], 'string'],
            [['address', 'wallet'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 255],
            [['address'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'address' => Yii::t('app', 'Address'),
            'name' => Yii::t('app', 'Name'),
            'details' => Yii::t('app', 'Details'),
            'wallet' => Yii::t('app', 'Wallet'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ValidatorsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ValidatorsQuery(get_called_class());
    }
}
