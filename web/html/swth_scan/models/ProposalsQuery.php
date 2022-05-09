<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Proposals]].
 *
 * @see Proposals
 */
class ProposalsQuery extends \yii\db\ActiveQuery
{
    public function active()
    {
        $subQuery = Votes::find()
            ->select(['proposal_id as pr_id', 'COUNT(*) AS cnt'])
            ->groupBy('proposal_id');

        return $this
            ->alias('p')
            ->innerJoin(['ap' => $subQuery], 'ap.pr_id = p.proposal_id AND cnt > 0');
    }

    /**
     * {@inheritdoc}
     * @return Proposals[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Proposals|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
