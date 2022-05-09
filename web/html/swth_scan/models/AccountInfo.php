<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "account_info".
 *
 * @property int $id
 * @property string|null $account
 * @property string|null $tr_first
 * @property string|null $tr_hash
 * @property string|null $username
 */
class AccountInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tr_first'], 'safe'],
            [['account', 'tr_hash'], 'string', 'max' => 64],
            [['username'], 'string', 'max' => 150],
            [['account'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'account' => Yii::t('app', 'Account'),
            'tr_first' => Yii::t('app', 'Tr First'),
            'tr_hash' => Yii::t('app', 'Tr Hash'),
            'username' => Yii::t('app', 'Username'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return AccountInfoQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AccountInfoQuery(get_called_class());
    }
}
