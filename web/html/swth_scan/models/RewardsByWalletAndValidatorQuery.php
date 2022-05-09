<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[RewardsByWalletAndValidator]].
 *
 * @see RewardsByWalletAndValidator
 */
class RewardsByWalletAndValidatorQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return RewardsByWalletAndValidator[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return RewardsByWalletAndValidator|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
