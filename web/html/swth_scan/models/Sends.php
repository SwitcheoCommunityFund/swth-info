<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sends".
 *
 * @property int|null $id
 * @property string|null $tr_hash
 * @property string|null $from
 * @property string|null $to
 * @property string|null $date
 * @property float|null $amount
 * @property string|null $denom
 */
class Sends extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sends';
    }

    public static function primaryKey()
    {
        return ['tr_hash','denom'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['date'], 'safe'],
            [['amount'], 'number'],
            [['denom'], 'string'],
            [['tr_hash', 'from', 'to'], 'string', 'max' => 64],
            [['tr_hash', 'denom'], 'unique', 'targetAttribute' => ['tr_hash', 'denom']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tr_hash' => Yii::t('app', 'Tr Hash'),
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'date' => Yii::t('app', 'Date'),
            'amount' => Yii::t('app', 'Amount'),
            'denom' => Yii::t('app', 'Denom'),
        ];
    }

    public function getToken()
    {
        return $this->hasOne(Tokens::className(), ['denom'=>'denom']);
    }

    /**
     * {@inheritdoc}
     * @return SendsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SendsQuery(get_called_class());
    }
}
