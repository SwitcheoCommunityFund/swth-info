<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\GroupedBonds */

$this->title = $model->wallet;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Unbonding wallets (grouped)'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="grouped-bonds-view">



    <br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'wallet',
            'value',
            'first_date',
            'last_date',
            'bonds_count',
        ],
    ]) ?>

</div>
