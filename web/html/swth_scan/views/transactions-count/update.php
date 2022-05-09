<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionsCount */

$this->title = Yii::t('app', 'Update Transactions Count: {name}', [
    'name' => $model->date,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transactions Counts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->date, 'url' => ['view', 'date' => $model->date, 'tr_type' => $model->tr_type]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="transactions-count-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
