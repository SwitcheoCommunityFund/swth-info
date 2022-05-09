<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GroupedBonds */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grouped-bonds-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'wallet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput() ?>

    <?= $form->field($model, 'first_date')->textInput() ?>

    <?= $form->field($model, 'last_date')->textInput() ?>

    <?= $form->field($model, 'bonds_count')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
