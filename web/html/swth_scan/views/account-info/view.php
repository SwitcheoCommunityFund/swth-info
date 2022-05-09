<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AccountInfo */

$this->title = $model->account;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="account-info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'account',
            'tr_first',
            'tr_hash',
            'username',
        ],
    ]) ?>

</div>
