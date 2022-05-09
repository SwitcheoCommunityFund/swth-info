<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */

$this->title = Yii::t('app', 'Update Sends: {name}', [
    'name' => $model->tr_hash,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sends'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tr_hash, 'url' => ['view', 'tr_hash' => $model->tr_hash, 'denom' => $model->denom]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sends-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
