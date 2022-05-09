<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByWalletAndValidatorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rewards-by-wallet-and-validator-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'wallet') ?>

    <?= $form->field($model, 'validator') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'last_award') ?>

    <?= $form->field($model, 'rewards_count') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
