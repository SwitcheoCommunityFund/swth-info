<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sends-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'tr_hash')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'denom')->dropDownList([ 'swth' => 'Swth', 'swth-n' => 'Swth-n', 'flm1' => 'Flm1', 'dbc2' => 'Dbc2', 'usdc1' => 'Usdc1', 'yam1' => 'Yam1', 'asa1' => 'Asa1', 'dbc1' => 'Dbc1', 'flm' => 'Flm', 'cgas1' => 'Cgas1', 'eth1' => 'Eth1', 'wbtc1' => 'Wbtc1', 'eth1-80-swth-20-lp1' => 'Eth1-80-swth-20-lp1', 'usdc1-80-swth-20-lp2' => 'Usdc1-80-swth-20-lp2', 'wbtc1-50-eth1-50-lp3' => 'Wbtc1-50-eth1-50-lp3', 'cel1' => 'Cel1', 'nex1' => 'Nex1', 'nneo1' => 'Nneo1', 'nneo2' => 'Nneo2', 'usdc1-50-eth1-50-lp4' => 'Usdc1-50-eth1-50-lp4', 'usdc1-50-wbtc1-50-lp5' => 'Usdc1-50-wbtc1-50-lp5', 'usdc1-50-cel1-50-lp6' => 'Usdc1-50-cel1-50-lp6', 'usdc1-50-nex1-50-lp7' => 'Usdc1-50-nex1-50-lp7', 'nneo2-50-usdc1-50-lp8' => 'Nneo2-50-usdc1-50-lp8', 'nneo2-50-swth-50-lp9' => 'Nneo2-50-swth-50-lp9', 'wbtc1-80-swth-20-lp10' => 'Wbtc1-80-swth-20-lp10', 'nneo2-25-swth-75-lp11' => 'Nneo2-25-swth-75-lp11', 'cel1-25-swth-75-lp12' => 'Cel1-25-swth-75-lp12', 'nex1-25-swth-75-lp13' => 'Nex1-25-swth-75-lp13', 'nneo2-50-eth1-50-lp14' => 'Nneo2-50-eth1-50-lp14', 'swth-b' => 'Swth-b', 'bnb1' => 'Bnb1', 'busd1' => 'Busd1', 'busd1-50-swth-50-lp15' => 'Busd1-50-swth-50-lp15', 'busd1-50-usdc1-50-lp16' => 'Busd1-50-usdc1-50-lp16', 'btcb1' => 'Btcb1', 'busd1-50-nneo2-50-lp17' => 'Busd1-50-nneo2-50-lp17', 'busd1-50-cel1-50-lp18' => 'Busd1-50-cel1-50-lp18', 'btcb1-50-wbtc1-50-lp19' => 'Btcb1-50-wbtc1-50-lp19', 'bnb1-50-eth1-50-lp20' => 'Bnb1-50-eth1-50-lp20', 'busdt1' => 'Busdt1', 'bhelmet1' => 'Bhelmet1', 'bbelt1' => 'Bbelt1', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
