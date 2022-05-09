<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AirdropLog */

$this->title = Yii::t('app', 'Create Airdrop Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Airdrop Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="airdrop-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
