<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AirdropLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="airdrop-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tx_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'air_time')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'wallet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'log')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
