<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\UnstakingStaked */

$this->title = $model->date;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Unstaking Stakeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="unstaking-staked-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'date',
            'wallet',
            'value',
            'type:ntext',
            'value_delegated',
            'count_delegated',
            'percent',
        ],
    ]) ?>

</div>
