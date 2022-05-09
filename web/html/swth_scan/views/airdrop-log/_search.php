<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AirdropLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="airdrop-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tx_id') ?>

    <?= $form->field($model, 'air_time') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'wallet') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'log') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
