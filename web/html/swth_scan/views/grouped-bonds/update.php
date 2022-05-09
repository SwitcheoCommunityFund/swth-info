<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GroupedBonds */

$this->title = Yii::t('app', 'Update Grouped Bonds: {name}', [
    'name' => $model->wallet,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grouped Bonds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wallet, 'url' => ['view', 'id' => $model->wallet]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="grouped-bonds-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
