<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfers */

$this->title = $model->wallet;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Transfers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="external-transfers-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'wallet',
            'amount',
            'blockchain',
            'denom',
            'fee_amount',
            'status',
            'timestamp',
            'transaction_hash',
            'transfer_type',
        ],
    ]) ?>

</div>
