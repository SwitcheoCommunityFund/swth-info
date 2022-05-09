<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Trades */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="trades-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'block_created_at')->textInput() ?>

    <?= $form->field($model, 'taker_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'taker_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'taker_fee_amount')->textInput() ?>

    <?= $form->field($model, 'taker_fee_denom')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'taker_side')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'maker_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'maker_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'maker_fee_amount')->textInput() ?>

    <?= $form->field($model, 'maker_fee_denom')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'maker_side')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'market')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'quantity')->textInput() ?>

    <?= $form->field($model, 'liquidation')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'block_height')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
