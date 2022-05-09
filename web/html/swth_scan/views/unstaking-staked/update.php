<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UnstakingStaked */

$this->title = Yii::t('app', 'Update Unstaking Staked: {name}', [
    'name' => $model->date,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Unstaking Stakeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->date, 'url' => ['view', 'date' => $model->date, 'wallet' => $model->wallet]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="unstaking-staked-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
