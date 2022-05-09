<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use onmotion\apexcharts\ApexchartsWidget;

$data=['First txs'=>[]];


foreach ($by_day as $item){

    $data['First txs'][]=[
        substr($item['date'],0,10),
        $item['count']
    ];
}

$series=[];
foreach ($data as $pack_name=>$data_pack) {
    $series[] = [
        'name' => $pack_name,
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
                ],
                'title' => [
                    'text' => 'Account info',
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

$data=['First txs'=>[]];


foreach ($by_month as $item){

    $data['First txs'][]=[
        substr($item['date'],0,10),
        $item['count']
    ];
}

$series=[];
foreach ($data as $pack_name=>$data_pack) {
    $series[] = [
        'name' => $pack_name,
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
                ],
                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
                'dataLabels' => [
                    'enabled' => true,
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
