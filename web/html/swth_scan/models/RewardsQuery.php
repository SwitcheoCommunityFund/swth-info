<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Rewards]].
 *
 * @see Rewards
 */
class RewardsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Rewards[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Rewards|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
