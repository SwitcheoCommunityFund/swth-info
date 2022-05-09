<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Delegates */

$this->title = Yii::t('app', 'Update Delegates: {name}', [
    'name' => $model->denom,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delegates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->denom, 'url' => ['view', 'denom' => $model->denom, 'tr_hash' => $model->tr_hash]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="delegates-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
