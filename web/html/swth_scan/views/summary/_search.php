<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SummarySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summary-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'wallet') ?>

    <?= $form->field($model, 'wait_unbonding_value') ?>

    <?= $form->field($model, 'unbonding_value') ?>

    <?= $form->field($model, 'rewards_value') ?>

    <?= $form->field($model, 'external_in') ?>

    <?php // echo $form->field($model, 'external_out') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
