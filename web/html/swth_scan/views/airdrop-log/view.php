<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AirdropLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Airdrop Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="airdrop-log-view">

    <h1><?= Html::encode($this->title) ?></h1>
    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tx_id',
            'air_time',
            'amount',
            'wallet',
            'status',
            //'log:ntext',
        ],
    ]) ?>

</div>
