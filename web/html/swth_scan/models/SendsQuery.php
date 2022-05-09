<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Sends]].
 *
 * @see Sends
 */
class SendsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Sends[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Sends|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
