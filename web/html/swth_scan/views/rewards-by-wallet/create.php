<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByWallet */

$this->title = Yii::t('app', 'Create Rewards By Wallet');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Wallets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rewards-by-wallet-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
