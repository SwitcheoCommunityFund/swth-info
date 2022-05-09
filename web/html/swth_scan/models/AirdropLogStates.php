<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "airdrop_log_states".
 *
 * @property int $id
 * @property string $state_code
 * @property string|null $description
 * @property string|null $semantic
 *
 * @property AirdropLog[] $airdropLogs
 */
class AirdropLogStates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'airdrop_log_states';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state_code'], 'required'],
            [['description'], 'string'],
            [['state_code'], 'string', 'max' => 255],
            [['semantic'], 'string', 'max' => 50],
            [['state_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'state_code' => Yii::t('app', 'State Code'),
            'description' => Yii::t('app', 'Description'),
            'semantic' => Yii::t('app', 'Semantic'),
        ];
    }

    /**
     * Gets query for [[AirdropLogs]].
     *
     * @return \yii\db\ActiveQuery|AirdropLogQuery
     */
    public function getAirdropLogs()
    {
        return $this->hasMany(AirdropLog::className(), ['status' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return AirdropLogStatesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AirdropLogStatesQuery(get_called_class());
    }
}
