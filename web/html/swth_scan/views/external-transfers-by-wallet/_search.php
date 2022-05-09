<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfersByWalletSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-transfers-by-wallet-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'wallet') ?>

    <?= $form->field($model, 'blockchain') ?>

    <?= $form->field($model, 'out') ?>

    <?= $form->field($model, 'in') ?>

    <?= $form->field($model, 'last_out') ?>

    <?php // echo $form->field($model, 'last_in') ?>

    <?php // echo $form->field($model, 'balance') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
