<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use app\helpers\ViewCommon;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AccountInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Account Info');
$this->params['breadcrumbs'][] = $this->title;



//<link href="/js/b24app/components/bootstrap/bootstrap4-toggle.min.css" rel="stylesheet">
//<script src="/js/b24app/components/bootstrap/bootstrap4-toggle.min.js"></script>

$this->registerCssFile('/css/loaders.css');
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS
    
    $(function(){
        loadCharts();    
    });
    
    function loadCharts()
    {
        $.ajax({
            url:'/account-info/charts',
            method:'POST',
            success:function(data){
                $('#charts_placement').html(data);
            },
            beforeSend: function(){ $('#charts_placement').html('<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>'); } 
        });
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
<div class="account-info-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>



    <?php
    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;
    ?>

    <div class="row" id="charts_placement"></div>

    <br>
    <br>

    <div class="row">
        <div class="col-md-8 switcher_group">
        </div>
        <div class="col-md-4">
            <p style="color:#a6a6a6" class="pull-right">Timezone: <?=$timezone?></p>
        </div>
    </div>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute'=>'account',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->account}?net=main' target='_blank'>{$data->account}</a>";
                }
            ],
            [
                'attribute'=>'tr_first',
                'format'=>'raw',
                'filter'=>DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'timeRange',
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
                    $date = new \DateTime($data->tr_first,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'tr_hash',
                'format'=>'raw',
                'value'=>function($data){
                    $tr_hash = substr($data->tr_hash,0,6).'...'.substr($data->tr_hash,-6);
                    return "<a href='https://switcheo.org/transaction/{$data->tr_hash}?net=main' title='{$data->tr_hash}' target='_blank'>{$tr_hash}</a>";
                }
            ],
            'username',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
