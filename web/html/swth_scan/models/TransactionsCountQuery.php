<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TransactionsCount]].
 *
 * @see TransactionsCount
 */
class TransactionsCountQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TransactionsCount[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TransactionsCount|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
