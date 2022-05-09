<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TokenPairs */

$this->title = Yii::t('app', 'Update Token Pairs: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Token Pairs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id, 'system' => $model->system]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="token-pairs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
