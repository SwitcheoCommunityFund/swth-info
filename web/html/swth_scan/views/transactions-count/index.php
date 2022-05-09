<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use app\helpers\ViewCommon;
use kartik\daterange\DateRangePicker;


/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionsCountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Transactions Counts');
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previous_tr_type = 'empty';

    $(document).on('click change','#precise_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });
    
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
        var tr_type = findGetParam('TransactionsCountSearch[tr_type]');
        if(tr_type==previous_tr_type) return;
        $.ajax({
            url:'/transactions-count/charts',
            method:'POST',
            data:{tr_type:tr_type},
            success:function(data){
                $('#charts_placement').html(data);
                previous_tr_type = tr_type;
            },
            beforeSend: function(){ $('#charts_placement').html('<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>'); } 
        });
    }

JS;


$this->registerJs($js,View::POS_READY);


$tr_types = ViewCommon::getTransactionTypes();

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

<div class="transactions-count-index">

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
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'tr_type',
                'filter' => Html::dropDownList($searchModel->formName() . '[tr_type]', $searchModel->tr_type, $tr_types,['prompt'=>'','class' => 'form-control']),
                'format'=>'raw'
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
            'count',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
