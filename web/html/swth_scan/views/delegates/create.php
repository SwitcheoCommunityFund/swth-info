<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Delegates */

$this->title = Yii::t('app', 'Create Delegates');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delegates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="delegates-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
