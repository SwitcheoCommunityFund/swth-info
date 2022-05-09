<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tokens */

$this->title = Yii::t('app', 'Create Tokens');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tokens-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
