<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ExternalTransfers */

$this->title = Yii::t('app', 'Update External Transfers: {name}', [
    'name' => $model->wallet,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'External Transfers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->wallet, 'url' => ['view', 'wallet' => $model->wallet, 'transaction_hash' => $model->transaction_hash]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="external-transfers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
