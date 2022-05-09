<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GroupedBondsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grouped-bonds-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'wallet') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'first_date') ?>

    <?= $form->field($model, 'last_date') ?>

    <?= $form->field($model, 'bonds_count') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
