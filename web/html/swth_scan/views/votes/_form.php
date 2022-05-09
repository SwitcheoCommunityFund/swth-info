<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Votes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="votes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tr_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'proposal_id')->textInput() ?>

    <?= $form->field($model, 'voter')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'option')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
