<?php

use yii\helpers\Html;
use yii\web\View;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use app\helpers\Formatters as frmt;
/* @var $searchModel app\models\BondsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Unstakes');
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
        var wallet = findGetParam('BondsSearch[wallet]');
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

<div class="bonds-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <div class="row" id="charts_placement">
        <?php
        echo $this->render('//site/day-month-charts',[
            'charts-controller'=>'bonds',
            'hide_series'=>['Staked'],
            'params'=>['wallet'=>@$_GET['BondsSearch']['wallet']]
        ]);
        ?>
    </div>
    <br>
    <br>



    <?php

    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;

    ?>

    <p style="color:#a6a6a6;float:right">Timezone: <?=$timezone?></p>

    <?php Pjax::begin(['timeout' => 5000 ]); ?>

    <?= GridView::widget([
        'tableOptions' => [
            'class' => 'table table-striped',
        ],
        'options' => [
            'class' => 'table-responsive',
            'style' => 'width:100%'
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'showFooter' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'wallet',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->wallet}?net=main' target='_blank'>{$data->wallet}</a>";
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
                'attribute'=>'date',
                'label'=>'Unstaking started',
                'format'=>'raw',
                'filter'=>DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'startTimeRange',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'timePicker'=>true,
                        'timePickerIncrement'=>30,
                        //'autoclose'=>true,
                        'locale'=>[
                            'format'=>'d.m.Y H:i'
                        ],
                    ],
                    'options'=>['class'=>'form-control','autocomplete'=>"off"]
                ]),
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->date,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'date',
                'label'=>'Unstaking ends',
                'format'=>'raw',
                'filter'=>DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'endTimeRange',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'timePicker'=>true,
                        'timePickerIncrement'=>30,
                        'locale'=>[
                            'format'=>'d.m.Y H:i'
                        ]
                    ],
                    'options'=>['class'=>'form-control','autocomplete'=>"off"]
                ]),
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->date,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->modify('+30 days')->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            'denom',
            [
                'attribute'=>'varState',
                'label'=>'state',
                'filter' => Html::dropDownList($searchModel->formName() . '[varState]', $searchModel->varState,['unstaked'=>'unstaked','waiting'=>'waiting'],['prompt'=>'','class' => 'form-control']),
                'format'=>'raw',
                'value'=>function($data){
                    $date = new \DateTime($data->date,(new \DateTimeZone('UTC')));
                    $date->modify('+30 days');
                    $unbonded = $date->getTimestamp();
                    $now = new \DateTime('now',(new \DateTimeZone('UTC')));
                    return '<span style="'.($unbonded<=$now->getTimestamp()?'color:green">unstaked':'color:gray">waiting').'</span>';
                }
            ],
            [
                'attribute'=>'time_to_unstake',
                'label'=>'Days Before Unstake',
                'format'=>'raw',
                'filter'=>false,
                'contentOptions'=>['style'=>'text-align:right'],
                'value'=>function($data){
                    $stake_start = new \DateTime($data->stake_start,(new \DateTimeZone('UTC')));
                    $stake_end = new \DateTime($data->stake_end,(new \DateTimeZone('UTC')));
                    return frmt::humanElapseDays($stake_start,$stake_end);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
