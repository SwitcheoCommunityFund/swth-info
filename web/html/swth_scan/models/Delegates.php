<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "delegates".
 *
 * @property int $id
 * @property string|null $wallet
 * @property string|null $validator
 * @property int|null $value
 * @property string|null $date
 * @property string|null $denom
 */
class Delegates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delegates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'value'], 'default', 'value' => null],
            [['id', 'value'], 'integer'],
            [['date'], 'safe'],
            [['denom'], 'string'],
            [['wallet', 'validator'], 'string', 'max' => 64],
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
            'validator' => Yii::t('app', 'Validator'),
            'value' => Yii::t('app', 'Value'),
            'date' => Yii::t('app', 'Date'),
            'denom' => Yii::t('app', 'Denom'),
        ];
    }

    public function getV()
    {
        return $this->hasOne(Validators::className(), ['address'=>'validator']);
    }

    public function getToken()
    {
        return $this->hasOne(Tokens::className(), ['denom'=>'denom']);
    }

    /**
     * {@inheritdoc}
     * @return DelegatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DelegatesQuery(get_called_class());
    }
}
