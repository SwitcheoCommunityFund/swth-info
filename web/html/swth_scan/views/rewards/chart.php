<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use onmotion\apexcharts\ApexchartsWidget;

$data=[];


foreach ($by_day as $item){
    if(!@$data[$item['denom']])
    {
        $data[$item['denom']]=[];
    }
    $data[$item['denom']][]=[
        substr($item['date'],0,10),
        round($item['value']/pow(10,(int)@$item['decimals']),2)
    ];
}


$other_denoms = array_keys($data);
unset($other_denoms[array_search('swth',$other_denoms)]);
$other_denoms = "'".implode("','",$other_denoms)."'";
//var_dump($data);

$series=[];
foreach ($data as $denom=>$data_pack){
    $series[]=[
        'name' => $denom,
        'data' => $data_pack,
    ];
}

/*$series = [
    [
        'name' => 'Day Rewards Amount',
        'data' => $data,
    ],
];*/

?>

<style>
    .panel {
        box-shadow: 0px 3px 6px -3px rgba(0,0,0,0.6);
        padding: 0px 15px 15px 15px;
    }
    .chart-panel{
        min-height:250px;
    }
</style>

<div class="col-md-6">
    <div class="panel chart-panel">
        <?= ApexchartsWidget::widget([
            'type' => 'area', // default area
            'height' => '250', // default 350
            //'width' => '500', // default 100%
            'timeout'=>200,
            'chartOptions' => [
                'chart' => [
                    'toolbar' => [
                        'show' => true,
                        'autoSelected' => 'zoom'
                    ],
                    'events' => [
                        'mounted' => "function (chartContext, config) {
                         var other_denoms = [{$other_denoms}];
                         for(var i in other_denoms){
                            chartContext.hideSeries(other_denoms[i]);
                         }
                      }"
                    ],
                ],
                'yaxis' => [
                    'labels'=> [
                        'formatter' => 'function(value) {
                            return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                        }'
                    ],
                ],
                'title' => [
                    'text' => 'Rewards',
                    'align' => 'left'
                ],
                'subtitle' => [
                    'text' => 'By day aggregation',
                    'align' => 'left'
                ],
                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
                'dataLabels' => [
                    'enabled' => false
                ],
                /*'stroke' => [
                    'show' => true,
                    'colors' => ['transparent']
                ],*/
                'legend' => [
                    'verticalAlign' => 'bottom',
                    'horizontalAlign' => 'left',
                ],
            ],
            'series' => $series
        ]); ?>
    </div>
</div>

<?php

$data=[];

foreach ($by_month as $item){
    if(!@$data[$item['denom']])
    {
        $data[$item['denom']]=[];
    }
    $data[$item['denom']][]=[
        substr($item['date'],0,10),
        round($item['value']/pow(10,(int)@$item['decimals']),2)
    ];
}

//var_dump($data);

$series=[];
foreach ($data as $denom=>$data_pack){
    $series[]=[
        'name' => $denom,
        'data' => $data_pack,
    ];
}
//var_dump($data);

/*$series = [
    [
        'name' => 'Month Rewards Amount',
        'data' => $data,
    ],
];*/

?>

<div class="col-md-6">
    <div class="panel chart-panel">
        <?= ApexchartsWidget::widget([
            'type' => 'bar', // default area
            'height' => '250', // default 350
            //'width' => '500', // default 100%
            'timeout'=>200,
            'chartOptions' => [
                'chart' => [
                    'toolbar' => [
                        'show' => true,
                        'autoSelected' => 'zoom'
                    ],
                    'events' => [
                        'mounted' => "function (chartContext, config) {
                         var other_denoms = [{$other_denoms}];
                         for(var i in other_denoms){
                            chartContext.hideSeries(other_denoms[i]);
                         }
                      }"
                    ],
                ],
                'yaxis' => [
                    'labels'=> [
                        'formatter' => 'function(value) {
                            return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                        }'
                    ],
                ],
                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
                'dataLabels' => [
                    'enabled' => true,
                    'formatter' => 'function(value) {
                        return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                    }',
                    /*'style' => [
                        'colors'=> ['#F44336', '#E91E63', '#9C27B0']
                    ]*/

                    'dropShadow' => [
                        'enabled' => true,
                        'top' => 0,
                        'left' => 0,
                        'blur' => 1,
                        'opacity' => 0.5
                    ]
                ],
                'plotOptions' => [
                    'bar' => [
                        'dataLabels' => [
                            'position' => 'top'
                        ]
                    ],
                ],
                'title' => [
                    'text' => 'Rewards',
                    'align' => 'left'
                ],
                'subtitle' => [
                    'text' => 'By month aggregation',
                    'align' => 'left'
                ],
                /*'stroke' => [
                    'show' => true,
                    'colors' => ['transparent']
                ],*/
                'legend' => [
                    'verticalAlign' => 'bottom',
                    'horizontalAlign' => 'left',
                ],
            ],
            'series' => $series
        ]) ?>
    </div>
</div>
