<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\GroupedBonds */

$this->title = Yii::t('app', 'Create Grouped Bonds');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Grouped Bonds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grouped-bonds-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
