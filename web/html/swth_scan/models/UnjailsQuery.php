<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Unjails]].
 *
 * @see Unjails
 */
class UnjailsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Unjails[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Unjails|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
