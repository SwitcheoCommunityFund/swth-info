<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Delegates */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="delegates-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'wallet')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'validator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput() ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'denom')->dropDownList([ 'swth' => 'Swth', 'swth-n' => 'Swth-n', 'flm1' => 'Flm1', 'dbc2' => 'Dbc2', 'usdc1' => 'Usdc1', 'yam1' => 'Yam1', 'asa1' => 'Asa1', 'dbc1' => 'Dbc1', 'flm' => 'Flm', 'cgas1' => 'Cgas1', 'eth1' => 'Eth1', 'wbtc1' => 'Wbtc1', 'eth1-80-swth-20-lp1' => 'Eth1-80-swth-20-lp1', 'usdc1-80-swth-20-lp2' => 'Usdc1-80-swth-20-lp2', 'wbtc1-50-eth1-50-lp3' => 'Wbtc1-50-eth1-50-lp3', 'cel1' => 'Cel1', 'nex1' => 'Nex1', 'nneo1' => 'Nneo1', 'nneo2' => 'Nneo2', 'usdc1-50-eth1-50-lp4' => 'Usdc1-50-eth1-50-lp4', 'usdc1-50-wbtc1-50-lp5' => 'Usdc1-50-wbtc1-50-lp5', 'usdc1-50-cel1-50-lp6' => 'Usdc1-50-cel1-50-lp6', 'usdc1-50-nex1-50-lp7' => 'Usdc1-50-nex1-50-lp7', 'nneo2-50-usdc1-50-lp8' => 'Nneo2-50-usdc1-50-lp8', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
