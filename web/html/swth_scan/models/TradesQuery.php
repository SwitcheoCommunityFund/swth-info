<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Trades]].
 *
 * @see Trades
 */
class TradesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Trades[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Trades|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
