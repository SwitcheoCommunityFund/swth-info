<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */

$this->title = $model->tr_hash;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sends'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sends-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <br>
    <br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tr_hash',
            'from',
            'to',
            'date',
            'amount',
            'denom',
        ],
    ]) ?>

</div>
