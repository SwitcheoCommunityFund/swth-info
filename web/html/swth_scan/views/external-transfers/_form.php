<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="external-transfers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'wallet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'blockchain')->dropDownList([ 'neo' => 'Neo', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'denom')->dropDownList([ 'swth' => 'Swth', 'swth-n' => 'Swth-n', 'flm1' => 'Flm1', 'dbc2' => 'Dbc2', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'fee_amount')->textInput() ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'timestamp')->textInput() ?>

    <?= $form->field($model, 'transaction_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'transfer_type')->dropDownList([ 'withdrawal' => 'Withdrawal', 'deposit' => 'Deposit', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
