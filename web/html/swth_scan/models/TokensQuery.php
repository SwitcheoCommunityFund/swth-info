<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Tokens]].
 *
 * @see Tokens
 */
class TokensQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Tokens[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Tokens|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
