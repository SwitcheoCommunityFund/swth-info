<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionsCount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transactions-count-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'count')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'tr_type')->dropDownList([ 'submit_proposal' => 'Submit proposal', 'withdraw' => 'Withdraw', 'send' => 'Send', 'sync_headers' => 'Sync headers', 'link_token' => 'Link token', 'delegate' => 'Delegate', 'begin_redelegate' => 'Begin redelegate', 'update_profile' => 'Update profile', 'create_validator' => 'Create validator', 'process_cross_chain_tx' => 'Process cross chain tx', 'sync_genesis' => 'Sync genesis', 'edit_validator' => 'Edit validator', 'vote' => 'Vote', 'withdraw_validator_commission' => 'Withdraw validator commission', 'begin_unbonding' => 'Begin unbonding', 'activate_sub_account' => 'Activate sub account', 'unjail' => 'Unjail', 'withdraw_delegator_reward' => 'Withdraw delegator reward', 'create_sub_account' => 'Create sub account', 'create_token' => 'Create token', 'set_max_validator_17' => 'Set max validator 17', 'enable_inflation' => 'Enable inflation', 'run_upgrade' => 'Run upgrade', 'stake_pool_token' => 'Stake pool token', 'claim_pool_rewards' => 'Claim pool rewards', 'create_oracle_vote' => 'Create oracle vote', 'deposit' => 'Deposit', 'add_market' => 'Add market', 'create_pool' => 'Create pool', 'link_pool' => 'Link pool', 'add_liquidity' => 'Add liquidity', 'set_reward_curve' => 'Set reward curve', 'set_rewards_weights' => 'Set rewards weights', 'set_commitment_curve' => 'Set commitment curve', 'set_trading' => 'Set trading', 'create_order' => 'Create order', 'cancel_order' => 'Cancel order', 'remove_liquidity' => 'Remove liquidity', 'unstake_pool_token' => 'Unstake pool token', 'cancel_all' => 'Cancel all', 'create_pool_with_liquidity' => 'Create pool with liquidity', 'create_oracle' => 'Create oracle', 'edit_order' => 'Edit order', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
