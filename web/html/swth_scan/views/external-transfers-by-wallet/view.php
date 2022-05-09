<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfersByWallet */

$this->title = $model->wallet;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Transfers By Wallets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="external-transfers-by-wallet-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'wallet',
            'blockchain',
            'out',
            'in',
            'last_out',
            'last_in',
            'balance',
        ],
    ]) ?>

</div>
