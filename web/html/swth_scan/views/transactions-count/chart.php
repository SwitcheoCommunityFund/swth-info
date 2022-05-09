<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use onmotion\apexcharts\ApexchartsWidget;

$data=[];

$exclude_labels=[];

foreach ($by_month as $item){
    if(!@$data[$item['tr_type']])
    {
        $data[$item['tr_type']]=[];
    }
    $data[$item['tr_type']][]=[
        $item['date'],
        $item['value']
    ];
    if(!in_array($item['tr_type'],$active_tr_types)){
        $exclude_labels[$item['tr_type']]=$item['tr_type'];
    }
}

$exclude_labels = "'".implode("','",array_flip($exclude_labels))."'";

$series=[];
foreach ($data as $tr_type=>$data_pack){
    $series[]=[
        'name' => $tr_type,
        'data' => $data_pack,
    ];
}


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
                         var exclude_labels = [{$exclude_labels}];
                         for(var i in exclude_labels){
                            chartContext.hideSeries(exclude_labels[i]);
                         }
                      }"
                    ],
                    'animations' => [
                        'enabled' => false
                    ]
                ],
                'yaxis' => [
                    /*'labels'=> [
                        'formatter' => 'function(value) {
                            return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                        }'
                    ],*/
                ],
                'title' => [
                    'text' => 'Transactions',
                    'align' => 'left'
                ],
                'subtitle' => [
                    'text' => 'By UTC date',
                    'align' => 'left'
                ],
                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
                'dataLabels' => [
                    'enabled' => false,
                ],
                /*'stroke' => [
                    'show' => true,
                    'colors' => ['transparent']
                ],*/
                'legend' => [
                    'verticalAlign' => 'right',
                    'horizontalAlign' => 'left',
                    'position'=>'right'
                ],
            ],
            'series' => $series
        ]); ?>
    </div>
</div>

<?php

$data=[];

$exclude_labels=[];

foreach ($by_month as $item){
    if(!@$data[$item['tr_type']])
    {
        $data[$item['tr_type']]=[];
    }
    $data[$item['tr_type']][]=[
        $item['date'],
        $item['value']
    ];
    if(!in_array($item['tr_type'],$active_tr_types)){
        $exclude_labels[$item['tr_type']]=$item['tr_type'];
    }
}

$exclude_labels = "'".implode("','",array_flip($exclude_labels))."'";

$series=[];
foreach ($data as $tr_type=>$data_pack){
    $series[]=[
        'name' => $tr_type,
        'data' => $data_pack,
    ];
}

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
                         var exclude_labels = [{$exclude_labels}];
                         for(var i in exclude_labels){
                            chartContext.hideSeries(exclude_labels[i]);
                         }
                      }"
                    ],
                    'animations' => [
                        'enabled' => false
                    ]
                ],
                'yaxis' => [
                    /*'labels'=> [
                        'formatter' => 'function(value) {
                            return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                        }'
                    ],*/
                ],
                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
                'dataLabels' => [
                    'enabled' => false,
                    /*'formatter' => 'function(value) {
                        return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                    }',*/
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
                    'text' => 'Transactions',
                    'align' => 'left'
                ],
                'subtitle' => [
                    'text' => 'By UTC month',
                    'align' => 'left'
                ],
                /*'stroke' => [
                    'show' => true,
                    'colors' => ['transparent']
                ],*/
                'legend' => [
                    'verticalAlign' => 'bottom',
                    'horizontalAlign' => 'left',
                    'position'=>'right'
                ],
            ],
            'series' => $series
        ]) ?>
    </div>
</div>
