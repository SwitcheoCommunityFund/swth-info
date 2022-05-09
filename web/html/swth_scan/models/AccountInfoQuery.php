<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[AccountInfo]].
 *
 * @see AccountInfo
 */
class AccountInfoQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AccountInfo[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AccountInfo|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
