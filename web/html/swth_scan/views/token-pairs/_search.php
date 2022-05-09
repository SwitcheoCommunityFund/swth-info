<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TokenPairsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="token-pairs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'system') ?>

    <?= $form->field($model, 'reserve_usd') ?>

    <?= $form->field($model, 'token0_symbol') ?>

    <?= $form->field($model, 'token0_id') ?>

    <?php // echo $form->field($model, 'token0_price') ?>

    <?php // echo $form->field($model, 'token0_decimals') ?>

    <?php // echo $form->field($model, 'reserve0') ?>

    <?php // echo $form->field($model, 'token1_symbol') ?>

    <?php // echo $form->field($model, 'token1_id') ?>

    <?php // echo $form->field($model, 'token1_price') ?>

    <?php // echo $form->field($model, 'token1_decimals') ?>

    <?php // echo $form->field($model, 'reserve1') ?>

    <?php // echo $form->field($model, 'updated') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
