<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\RewardsByValidator */

$this->title = Yii::t('app', 'Create Rewards By Validator');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rewards By Validators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rewards-by-validator-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
