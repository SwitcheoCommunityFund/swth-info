<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[AirdropLogStates]].
 *
 * @see AirdropLogStates
 */
class AirdropLogStatesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AirdropLogStates[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AirdropLogStates|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
