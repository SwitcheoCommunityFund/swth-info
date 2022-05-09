<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tokens');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .token_img{
        max-width: 50px;
    }
</style>

<div class="tokens-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Tokens'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'class' => 'table-responsive',
            'style' => 'width:100%'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'denom',
            'blockchain',
            'decimals',
            'chain_id',
            'originator',
            'asset_id',
            'lock_proxy_hash',
            [
                'attribute'=>'image',
                'format'=>'raw',
                'value'=>function($data){
                    return "<img class='token_img' src='/img/tokens/$data->image'>";
                }
            ],
            'coin_gecko_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
