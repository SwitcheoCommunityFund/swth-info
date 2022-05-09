<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TokensSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tokens-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'denom') ?>

    <?= $form->field($model, 'blockchain') ?>

    <?= $form->field($model, 'decimals') ?>

    <?php // echo $form->field($model, 'chain_id') ?>

    <?php // echo $form->field($model, 'originator') ?>

    <?php // echo $form->field($model, 'asset_id') ?>

    <?php // echo $form->field($model, 'lock_proxy_hash') ?>

    <?php // echo $form->field($model, 'image') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
