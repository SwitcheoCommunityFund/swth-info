<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Votes */

$this->title = $model->tr_hash;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Votes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="votes-view">

    <h2><?= Html::encode($this->title) ?></h2>

    <br>
    <br>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tr_hash',
            'proposal_id',
            'voter',
            'option',
            'date',
        ],
    ]) ?>

</div>
