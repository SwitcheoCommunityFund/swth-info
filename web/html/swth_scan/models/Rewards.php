<?php

namespace app\models;

use Yii;
use app\models\Validators;

/**
 * This is the model class for table "rewards".
 *
 * @property int $id
 * @property string|null $wallet
 * @property string|null $validator
 * @property string|null $date
 * @property int|null $value
 * @property string|null $denom
 */
class Rewards extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rewards';
    }

    public static function primaryKey()
    {
        return ['id','denom'];
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
            [['date','id'], 'safe'],
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
            'date' => Yii::t('app', 'Date'),
            'value' => Yii::t('app', 'Value'),
            'denom' => Yii::t('app', 'Denom'),
            'validatorName' => Yii::t('app', 'Validator'),
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
     * @return RewardsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RewardsQuery(get_called_class());
    }
}
