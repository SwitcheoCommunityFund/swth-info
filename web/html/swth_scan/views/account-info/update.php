<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AccountInfo */

$this->title = Yii::t('app', 'Update Account Info: {name}', [
    'name' => $model->account,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->account, 'url' => ['view', 'id' => $model->account]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="account-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
