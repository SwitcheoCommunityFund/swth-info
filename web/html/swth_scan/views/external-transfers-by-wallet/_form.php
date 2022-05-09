<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfersByWallet */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-transfers-by-wallet-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'wallet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'blockchain')->dropDownList([ 'neo' => 'Neo', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'out')->textInput() ?>

    <?= $form->field($model, 'in')->textInput() ?>

    <?= $form->field($model, 'last_out')->textInput() ?>

    <?= $form->field($model, 'last_in')->textInput() ?>

    <?= $form->field($model, 'balance')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
