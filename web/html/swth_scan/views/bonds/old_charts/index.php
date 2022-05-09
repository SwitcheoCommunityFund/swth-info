<?php

use yii\helpers\Html;
use yii\web\View;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BondsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Unstakes');
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('/css/loaders.css');
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previousWallet = 'empty';
    
    $(document).on('pjax:send', function() {
      $('#loading').show()
      loadCharts();    
    });
    $(document).on('pjax:complete', function() {
      $('#loading').hide()
    });
    
    $(function(){
        loadCharts();    
    });
    
    function loadCharts()
    {
        var wallet = findGetParam('BondsSearch[wallet]');
        if(wallet==previousWallet) return;
        $.ajax({
            url:'/bonds/charts',
            method:'POST',
            data:{wallet:wallet},
            success:function(data){
                $('#charts_placement').html(data);
                previousWallet = wallet;
            },
            beforeSend: function(){ $('#charts_placement').html('<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>'); } 
        });
    }

JS;


$this->registerJs($js,View::POS_READY);

function humanElapse($from, $to, $full=false)
{
    $i = [];
    $a = $from->diff($to);
    if($a->invert) return '0 days';

    if($a->y) $i[]=prettyTime('Year',   $a->y);
    if($a->m) $i[]=prettyTime('Month',  $a->m);
    if($a->d) $i[]=prettyTime('Day',    $a->d);
    if($a->h) $i[]=prettyTime('Hour',   $a->h);
    if($a->i) $i[]=prettyTime('Minute', $a->i);
    if($a->s) $i[]=prettyTime('Second', $a->s);

    return $full?implode($i,'&nbsp;'):@$i[0];

}

function humanElapseDays($from, $to){
    $i = 0;
    $a = $from->diff($to);
    if($a->invert) return '0 days';
    return prettyTime('Day', $a->days);
}

function prettyTime($type,$count,$nbsp=true){
    return $count.($nbsp?'&nbsp;':' ').$type.($count>1||$count==0?'s':'');
}


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

    <div class="row" id="charts_placement"></div>

    <br>
    <br>


    <?php Pjax::begin(); ?>
    <?php

    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;

    ?>

    <p style="color:#a6a6a6;float:right">Timezone: <?=$timezone?></p>


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
                    return humanElapseDays($stake_start,$stake_end);
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
