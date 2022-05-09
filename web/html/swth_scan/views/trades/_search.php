<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TradesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trades-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'block_created_at') ?>

    <?= $form->field($model, 'taker_id') ?>

    <?= $form->field($model, 'taker_address') ?>

    <?= $form->field($model, 'taker_fee_amount') ?>

    <?php // echo $form->field($model, 'taker_fee_denom') ?>

    <?php // echo $form->field($model, 'taker_side') ?>

    <?php // echo $form->field($model, 'maker_id') ?>

    <?php // echo $form->field($model, 'maker_address') ?>

    <?php // echo $form->field($model, 'maker_fee_amount') ?>

    <?php // echo $form->field($model, 'maker_fee_denom') ?>

    <?php // echo $form->field($model, 'maker_side') ?>

    <?php // echo $form->field($model, 'market') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'quantity') ?>

    <?php // echo $form->field($model, 'liquidation') ?>

    <?php // echo $form->field($model, 'block_height') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
