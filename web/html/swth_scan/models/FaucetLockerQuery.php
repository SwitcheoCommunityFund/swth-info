<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[FaucetLocker]].
 *
 * @see FaucetLocker
 */
class FaucetLockerQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return FaucetLocker[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return FaucetLocker|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
