<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Summary]].
 *
 * @see Summary
 */
class SummaryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Summary[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Summary|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
