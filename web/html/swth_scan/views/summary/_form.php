<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Summary */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="summary-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'wallet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wait_unbonding_value')->textInput() ?>

    <?= $form->field($model, 'unbonding_value')->textInput() ?>

    <?= $form->field($model, 'rewards_value')->textInput() ?>

    <?= $form->field($model, 'external_in')->textInput() ?>

    <?= $form->field($model, 'external_out')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
