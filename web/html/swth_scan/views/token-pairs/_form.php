<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TokenPairs */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="token-pairs-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'system')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reserve_usd')->textInput() ?>

    <?= $form->field($model, 'token0_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token0_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token0_price')->textInput() ?>

    <?= $form->field($model, 'token0_decimals')->textInput() ?>

    <?= $form->field($model, 'reserve0')->textInput() ?>

    <?= $form->field($model, 'token1_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token1_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token1_price')->textInput() ?>

    <?= $form->field($model, 'token1_decimals')->textInput() ?>

    <?= $form->field($model, 'reserve1')->textInput() ?>

    <?= $form->field($model, 'updated')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
