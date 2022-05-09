<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByWalletAndMonth */

$this->title = Yii::t('app', 'Create Rewards By Wallet And Month');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Wallet And Months'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rewards-by-wallet-and-month-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
