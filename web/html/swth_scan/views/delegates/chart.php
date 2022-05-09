<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use onmotion\apexcharts\ApexchartsWidget;

$data_s=[];
$data_u=[];
$data_d=[];

foreach ($delegate_by_day as $item) {
    $data_d[] = [
        $item['date'],
        //substr($item['date'], 0, 10),
        round($item['value'] / pow(10, 8), 2)
    ];
}


$session = Yii::$app->session;
$timezone = $session['timezone_name'];
$timezone = empty($timezone)?'UTC':$timezone;

$date = new \DateTime(date('Y-m-d H:i:s'),(new \DateTimeZone('UTC')));
$date->setTimezone((new \DateTimeZone($timezone)));
$current_date = $date->format('U');


$series = [
    [
        'name' => 'Staked',
        'data' => $data_d,
    ],
];

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
                    'zoom' => [
                        'type' => 'x',
                        'enabled' => true,
                        'autoScaleYaxis' => true
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
                    'text' => 'Stakes',
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
                'annotations'=> [
                    'xaxis'=>[
                        [
                            'x' => $current_date.'000',
                            'strokeDashArray' => 0,
                            'borderColor' => '#775DD0',
                            'label' => [
                                'borderColor' => '#775DD0',
                                'style' => [
                                    'color' => '#fff',
                                    'background' => '#775DD0',
                                ],
                                'text' => 'Today',
                            ]
                        ]
                    ]
                ],
                'tooltip'=>[
                    'shared'=> true
                ]
            ],
            'series' => $series
        ]); ?>
    </div>
</div>

<?php

$data_d=[];

foreach ($delegate_by_month as $item){
    $data_d[] = [
        substr($item['date'], 0, 10),
        round($item['value'] / pow(10, 8), 2)
    ];
}


//var_dump($data);

$series = [
    [
        'name' => 'Staked',
        'data' => $data_d,
    ],
];

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
                    
                    'zoom' => [
                        'type' => 'x',
                        'enabled' => true,
                        'autoScaleYaxis' => true
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
                    'enabled' => false,
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
                    'text' => 'Stakes',
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
