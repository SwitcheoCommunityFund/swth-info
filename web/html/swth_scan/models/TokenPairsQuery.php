<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TokenPairs]].
 *
 * @see TokenPairs
 */
class TokenPairsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TokenPairs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TokenPairs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
