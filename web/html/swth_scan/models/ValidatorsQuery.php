<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Validators]].
 *
 * @see Validators
 */
class ValidatorsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Validators[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Validators|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
