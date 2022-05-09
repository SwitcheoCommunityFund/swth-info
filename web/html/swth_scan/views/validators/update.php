<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Validators */

$this->title = Yii::t('app', 'Update Validators: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Validators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->address]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="validators-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
