<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sends */

$this->title = Yii::t('app', 'Create Sends');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sends'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sends-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
