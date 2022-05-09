<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[VotersTop]].
 *
 * @see VotersTop
 */
class VotersTopQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return VotersTop[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return VotersTop|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
