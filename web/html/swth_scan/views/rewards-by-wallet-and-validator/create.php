<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByWalletAndValidator */

$this->title = Yii::t('app', 'Create Rewards By Wallet And Validator');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Wallet And Validators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rewards-by-wallet-and-validator-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
