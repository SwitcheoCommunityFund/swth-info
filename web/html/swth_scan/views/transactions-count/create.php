<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionsCount */

$this->title = Yii::t('app', 'Create Transactions Count');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Transactions Counts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transactions-count-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
