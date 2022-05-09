<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Delegates]].
 *
 * @see Delegates
 */
class DelegatesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Delegates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Delegates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
