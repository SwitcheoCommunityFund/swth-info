<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bonds */

$this->title = Yii::t('app', 'Create Bonds');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bonds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bonds-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
