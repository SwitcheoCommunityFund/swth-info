<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfersByWallet */

$this->title = Yii::t('app', 'Create External Transfers By Wallet');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Transfers By Wallets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-transfers-by-wallet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
