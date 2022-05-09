<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByValidator */

$this->title = Yii::t('app', 'Update Rewards By Validator: {name}', [
    'name' => $model->validator,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Validators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->validator, 'url' => ['view', 'id' => $model->validator]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="rewards-by-validator-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
