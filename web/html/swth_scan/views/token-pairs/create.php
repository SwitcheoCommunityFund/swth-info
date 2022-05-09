<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TokenPairs */

$this->title = Yii::t('app', 'Create Token Pairs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Token Pairs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="token-pairs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
