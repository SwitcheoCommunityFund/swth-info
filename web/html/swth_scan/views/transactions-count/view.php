<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionsCount */

$this->title = $model->date;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transactions Counts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="transactions-count-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'count',
            'date',
            'tr_type',
        ],
    ]) ?>

</div>
