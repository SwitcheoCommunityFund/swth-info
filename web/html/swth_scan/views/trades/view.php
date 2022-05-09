<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Trades */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Trades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="trades-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'block_created_at',
            'taker_id',
            'taker_address',
            'taker_fee_amount',
            'taker_fee_denom',
            'taker_side',
            'maker_id',
            'maker_address',
            'maker_fee_amount',
            'maker_fee_denom',
            'maker_side',
            'market',
            'price',
            'quantity',
            'liquidation:ntext',
            'block_height',
        ],
    ]) ?>

</div>
