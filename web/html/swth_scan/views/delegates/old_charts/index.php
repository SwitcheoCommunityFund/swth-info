<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use app\helpers\ViewCommon;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
/* @var $this yii\web\View */
/* @var $searchModel app\models\DelegatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stakes');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
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
        var wallet = findGetParam('DelegatesSearch[wallet]');
        if(wallet==previousWallet) return;
        $.ajax({
            url:'/delegates/charts',
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

$denoms = ViewCommon::getDenoms();

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


<div class="delegates-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php
    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;
    ?>

    <div class="row" id="charts_placement"></div>

    <br>
    <br>

    <?php Pjax::begin(); ?>
    <div class="col-md-12">
        <p style="color:#a6a6a6; float:right">Timezone: <?=$timezone?></p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-striped',
        ],
        'options' => [
            'class' => 'table-responsive',
            'style' => 'width:100%'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute'=>'wallet',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->wallet}?net=main' target='_blank'>{$data->wallet}</a>";
                }
            ],
            [
                'attribute'=>'validatorName',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->validator}?net=main' target='_blank'>{$data->v->name}</a>";
                }
            ],
            [
                'attribute'=>'date',
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
                    $date = new \DateTime($data->date,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'value',
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->value==null?null:$data->value/pow(10,(int)@$data->token->decimals);
                }
            ],
            [
                'attribute'=>'denom',
                'filter' => Html::dropDownList($searchModel->formName() . '[denom]', $searchModel->denom, $denoms,['prompt'=>'','class' => 'form-control']),
                'format'=>'raw'
            ],
            //'tr_hash',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
