<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UnstakingStaked]].
 *
 * @see UnstakingStaked
 */
class UnstakingStakedQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UnstakingStaked[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UnstakingStaked|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
