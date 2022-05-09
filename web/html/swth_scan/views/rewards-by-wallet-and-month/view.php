<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByWalletAndMonth */

$this->title = $model->wallet;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Wallet And Months'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rewards-by-wallet-and-month-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'wallet',
            'value',
            'month',
            'rewards_count',
        ],
    ]) ?>

</div>
