<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ExternalTransfers]].
 *
 * @see ExternalTransfers
 */
class ExternalTransfersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ExternalTransfers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ExternalTransfers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
