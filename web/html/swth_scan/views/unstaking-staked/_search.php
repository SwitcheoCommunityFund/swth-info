<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UnstakingStakedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="unstaking-staked-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'date') ?>

    <?= $form->field($model, 'wallet') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'value_delegated') ?>

    <?php // echo $form->field($model, 'count_delegated') ?>

    <?php // echo $form->field($model, 'percent') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
