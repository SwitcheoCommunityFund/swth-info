<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RewardsByWallet]].
 *
 * @see RewardsByWallet
 */
class RewardsByWalletQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RewardsByWallet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RewardsByWallet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
