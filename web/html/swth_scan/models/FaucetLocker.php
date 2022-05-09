<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "faucet_locker".
 *
 * @property int $id
 * @property string $wallet
 * @property string|null $lock_until
 * @property int|null $count_success
 * @property int|null $count_tries
 */
class FaucetLocker extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'faucet_locker';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet'], 'required'],
            [['lock_until'], 'safe'],
            [['count_success', 'count_tries'], 'default', 'value' => null],
            [['count_success', 'count_tries'], 'integer'],
            [['wallet'], 'string', 'max' => 64],
            [['wallet'], 'unique'],
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
            'lock_until' => Yii::t('app', 'Lock Until'),
            'count_success' => Yii::t('app', 'Count Success'),
            'count_tries' => Yii::t('app', 'Count Tries'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return FaucetLockerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FaucetLockerQuery(get_called_class());
    }
}
