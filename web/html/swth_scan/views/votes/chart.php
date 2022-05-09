<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use onmotion\apexcharts\ApexchartsWidget;


$series=[];
$labels=[];


foreach ($votes as $item){
    $series[]=$item['count'];
    $labels[]=$item['option'];
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


<div class="panel chart-panel">
    <?= ApexchartsWidget::widget([
        'type' => 'donut', // default area
        'height' => '250', // default 350
        //'width' => '500', // default 100%
        'timeout'=>200,
        'chartOptions' => [
            'labels'=>$labels,
            'chart' => [
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'title' => [
                'text' => 'Votes',
                'align' => 'left'
            ],
            'legend' => [
                'verticalAlign' => 'bottom',
                'horizontalAlign' => 'left',
            ],
        ],
        'series' => $series
    ]); ?>
</div>
