<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tokens".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $denom
 * @property string|null $blockchain
 * @property int|null $decimals
 * @property int|null $chain_id
 * @property string|null $originator
 * @property string|null $asset_id
 * @property string|null $lock_proxy_hash
 * @property string|null $image
 */
class Tokens extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['denom', 'blockchain','coin_gecko_id'], 'string'],
            [['decimals', 'chain_id'], 'default', 'value' => null],
            [['decimals', 'chain_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['originator', 'asset_id', 'lock_proxy_hash'], 'string', 'max' => 64],
            [['denom', 'blockchain'], 'unique', 'targetAttribute' => ['denom', 'blockchain']],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'denom' => Yii::t('app', 'Denom'),
            'blockchain' => Yii::t('app', 'Blockchain'),
            'decimals' => Yii::t('app', 'Decimals'),
            'chain_id' => Yii::t('app', 'Chain ID'),
            'originator' => Yii::t('app', 'Originator'),
            'asset_id' => Yii::t('app', 'Asset ID'),
            'lock_proxy_hash' => Yii::t('app', 'Lock Proxy Hash'),
            'image' => Yii::t('app', 'Image'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return TokensQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TokensQuery(get_called_class());
    }
}
