<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Markets]].
 *
 * @see Markets
 */
class MarketsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Markets[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Markets|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
