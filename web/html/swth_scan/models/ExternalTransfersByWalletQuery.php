<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ExternalTransfersByWallet]].
 *
 * @see ExternalTransfersByWallet
 */
class ExternalTransfersByWalletQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExternalTransfersByWallet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExternalTransfersByWallet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
