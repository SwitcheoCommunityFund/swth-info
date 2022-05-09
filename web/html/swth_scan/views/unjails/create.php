<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Unjails */

$this->title = Yii::t('app', 'Create Unjails');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Unjails'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="unjails-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
