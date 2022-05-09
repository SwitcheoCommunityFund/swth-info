<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByValidator */

$this->title = $model->validator;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Validators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rewards-by-validator-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'validator',
            'value',
            'last_award',
            'rewards_count',
        ],
    ]) ?>

</div>
