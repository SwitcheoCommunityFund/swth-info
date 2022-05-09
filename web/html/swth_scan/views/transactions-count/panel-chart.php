<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use onmotion\apexcharts\ApexchartsWidget;

$data=[];

$exclude_labels=[];

foreach ($by_month as $item){
    $data['transactions count'][]=[
        $item['date'],
        $item['value']
    ];
}

$series=[];
foreach ($data as $tr_type=>$data_pack){
    $series[]=[
        'name' => $tr_type,
        'data' => $data_pack,
    ];
}


?>

<div class="col-md-12">
    <div class="chart-panel">
        <?= ApexchartsWidget::widget([
            'type' => 'line', // default area
            'height' => '150', // default 350
            //'width' => '500', // default 100%
            'timeout'=>200,
            'chartOptions' => [
                'chart' => [
                    'toolbar' => [
                        'show' => false,
                        'autoSelected' => 'zoom'
                    ],
                    'zoom' => [
                        'enabled'=>false
                    ],
                    'sparkline' => [
                        'enabled'=>true
                    ],
                ],
                'yaxis' => [
                    /*'labels'=> [
                        'formatter' => 'function(value) {
                            return (new Intl.NumberFormat(\'en-US\',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
                        }'
                    ],*/
                ],
                'title' => [
                    'text' => 'Last 6 months',
                    'align' => 'left',
                    'offsetX' => 10,
                    'style'=>[
                        'color' => '#a2a2a2',
                        'fontWeight'=>'300'
                        //'margin-left' => '10px'
                    ]
                ],
                /*'subtitle' => [
                    'enabled'=>false
                ],*/
                'xaxis' => [
                    'type' => 'datetime',
                    // 'categories' => $categories,
                ],
                'dataLabels' => [
                    'enabled' => false,
                ],
                'stroke' => [
                    //'show' => true,
                    //'colors' => ['transparent'],
                    'curve' => 'smooth'
                ],
                'legend' => [
                    'enabled' => false,
                ],
            ],
            'series' => $series
        ]); ?>
    </div>
</div>

