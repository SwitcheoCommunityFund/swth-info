<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfersByWallet */

$this->title = Yii::t('app', 'Update External Transfers By Wallet: {name}', [
    'name' => $model->wallet,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Transfers By Wallets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wallet, 'url' => ['view', 'wallet' => $model->wallet, 'blockchain' => $model->blockchain]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="external-transfers-by-wallet-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
