<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Bonds]].
 *
 * @see Bonds
 */
class BondsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Bonds[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Bonds|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
