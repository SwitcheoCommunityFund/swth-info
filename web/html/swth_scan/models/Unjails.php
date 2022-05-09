<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unjails".
 *
 * @property int $id
 * @property string|null $validator
 * @property string|null $wallet
 * @property string|null $date
 */
class Unjails extends \yii\db\ActiveRecord
{
    public $validatorName= '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unjails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['date'], 'safe'],
            [['validator', 'wallet'], 'string', 'max' => 64],
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
            'validator' => Yii::t('app', 'Validator'),
            'wallet' => Yii::t('app', 'Wallet'),
            'date' => Yii::t('app', 'Date'),
        ];
    }


    public function getV()
    {
        return $this->hasOne(Validators::className(), ['address'=>'validator']);
    }

    /**
     * {@inheritdoc}
     * @return UnjailsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UnjailsQuery(get_called_class());
    }
}
