<?php

use yii\helpers\Html;
use yii\web\View;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UnstakingStakedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Unstakes With Delegated');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/loaders.css');
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previousWallet = 'empty';
    
    $(document).on('pjax:send', function() {
      reloadCharts();    
    });
    
    $(document).on('pjax:complete', function() {
    });
    
    function reloadCharts()
    {
        var wallet = findGetParam('UnstakingStaked[wallet]');
        if(wallet==previousWallet) return;
        loadDailyChart({wallet:wallet});
        loadMonthlyChart({wallet:wallet});
        previousWallet = wallet; 
    }

JS;


$this->registerJs($js,View::POS_READY);


?>

<style>
    .switcher_group {
        margin-bottom:15px
    }
    #charts_placement {
        min-height: 300px;
    }
    .loader {
        margin: auto;
        width:100px
    }
</style>


<div class="unstaking-staked-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <br>

    <div class="row" id="charts_placement">
        <?php
            echo $this->render('//charts/day-month',[
                'charts_controller'=>'bonds',
                'hide_series'=>['Staked'],
                'params'=>['wallet'=>@$_GET['UnstakingStaked']['wallet']]
            ]);
        ?>
    </div>

    <br>
    <br>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'date',
                'format'=>['date', 'php:d.m.Y']
            ],
            [
                'attribute'=>'wallet',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->wallet}?net=main' target='_blank'>{$data->wallet}</a>";
                }
            ],
            [
                'attribute'=>'percent',
                'format'=>'raw',
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){return $data->percent.'%';}
            ],
            [
                'attribute'=>'value_delegated',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->value_delegated/pow(10,8);
                }
            ],
            [
                'attribute'=>'value',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->value/pow(10,8);
                }
            ],
            [
                'attribute'=>'delegation_balance',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->delegation_balance/pow(10,8);
                }
            ],
            'count_delegated',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
