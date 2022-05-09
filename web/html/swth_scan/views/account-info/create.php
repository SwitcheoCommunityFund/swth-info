<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AccountInfo */

$this->title = Yii::t('app', 'Create Account Info');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
