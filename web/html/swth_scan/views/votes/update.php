<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Votes */

$this->title = Yii::t('app', 'Update Votes: {name}', [
    'name' => $model->tr_hash,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Votes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tr_hash, 'url' => ['view', 'id' => $model->tr_hash]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="votes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
