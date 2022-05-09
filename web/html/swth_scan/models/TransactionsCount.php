<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transactions_count".
 *
 * @property int $id
 * @property int|null $count
 * @property string $date
 * @property string $tr_type
 */
class TransactionsCount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions_count';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count'], 'default', 'value' => null],
            [['count'], 'integer'],
            [['date', 'tr_type'], 'required'],
            [['date'], 'safe'],
            [['tr_type'], 'string'],
            [['date', 'tr_type'], 'unique', 'targetAttribute' => ['date', 'tr_type']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'count' => Yii::t('app', 'Count'),
            'date' => Yii::t('app', 'Date'),
            'tr_type' => Yii::t('app', 'Tr Type'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TransactionsCountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TransactionsCountQuery(get_called_class());
    }
}
