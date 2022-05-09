<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByWalletAndValidator */

$this->title = Yii::t('app', 'Update Rewards By Wallet And Validator: {name}', [
    'name' => $model->wallet,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Wallet And Validators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wallet, 'url' => ['view', 'wallet' => $model->wallet, 'validator' => $model->validator]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="rewards-by-wallet-and-validator-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
