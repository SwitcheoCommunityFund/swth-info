<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RewardsByWalletAndMonth]].
 *
 * @see RewardsByWalletAndMonth
 */
class RewardsByWalletAndMonthQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RewardsByWalletAndMonth[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RewardsByWalletAndMonth|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
