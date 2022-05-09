<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ChainDenomDecimal]].
 *
 * @see ChainDenomDecimal
 */
class ChainDenomDecimalQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ChainDenomDecimal[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChainDenomDecimal|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
