<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RewardsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rewards');
$this->params['breadcrumbs'][] = $this->title;



//<link href="/js/b24app/components/bootstrap/bootstrap4-toggle.min.css" rel="stylesheet">
//<script src="/js/b24app/components/bootstrap/bootstrap4-toggle.min.js"></script>

$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previousWallet = 'empty';
    
    $(document).on('click change','#empty_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });

    $(document).on('click change','#precise_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });

    $(document).on('click','#excel_export',function()
    {
        
       var wallet = findGetParam('RewardsSearch[wallet]')
       if(wallet==null || wallet==''){
           alert('Please add your wallet name into the search field');
       } else {
           
           location.href = 
                location.origin 
                + '/rewards'
                + '/excel-export' 
                + location.search;
       }
    });
    
    
    $(document).on('pjax:send', function() {
      $('#loading').show()
      loadRewardsCharts();    
    });
    $(document).on('pjax:complete', function() {
      $('#loading').hide()
    });
    
    $(function(){
        loadRewardsCharts();    
    });
    
    function loadRewardsCharts()
    {
        var wallet = findGetParam('RewardsSearch[wallet]');
        if(wallet==previousWallet) return;
        $.ajax({
            url:'/rewards/charts',
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

<div class="rewards-index">

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
            <div class="col-md-12">
                <div class="col-md-3">
                    <label>Empty values </label>
                </div>
                <div class="col-md-9">
                    <input id='empty_switcher'
                           data-size="xs"
                           type='checkbox'
                           data-width="70"
                           data-on="included"
                           data-onstyle="success"
                           data-off="excluded"
                           data-offstyle="info"
                           data-toggle='toggle'
                           name="<?= $searchModel->formName().'[varEmptyValues]'?>"
                        <?= $searchModel->varEmptyValues?'checked':null; ?>
                    />
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label>Precise values </label>
                </div>
                <div class="col-md-9" >
                    <input id='precise_switcher'
                           data-size="xs"
                           type='checkbox'
                           data-width="70"
                           data-onstyle="success"
                           data-offstyle="info"
                           data-toggle='toggle'
                           name="<?= $searchModel->formName().'[varPreciseValues]'?>"
                        <?= $searchModel->varPreciseValues?'checked':null; ?>
                    />
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <span class="btn btn-primary btn-xs" style="float:right" id="excel_export">
                <i class="glyphicon glyphicon-download-alt"></i>&nbsp;Excel Export
            </span>
            <p style="color:#a6a6a6">Timezone: <?=$timezone?></p>
        </div>
    </div>



    <?php Pjax::begin(); ?>


    <?php

    $columns = [
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
            //'attribute'=>'validator',
            'attribute'=>'validatorName',
            'format'=>'raw',
            'value'=>function($data){
                return "<a href='https://switcheo.org/validator/{$data->validator}?net=main' target='_blank'>{$data->v->name}</a>";
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
                return $data->value==null?null:$data->value/pow(10,8);
            }
        ],
    ];

    /*if($searchModel->varPreciseValues){
        $columns[]=[
            'attribute'=>'value',
            'format'=>['decimal',8],
            'contentOptions'=>['style' => 'text-align: right;'],
            'value'=>function($data){
                return $data->value==null?null:$data->value/pow(10,8);
            }
        ];
    }*/

    $columns[]=[
        'attribute'=>'tr_type',
        'filter' => Html::dropDownList($searchModel->formName() . '[tr_type]', $searchModel->tr_type,
            [
                'withdraw_delegator_reward' =>'withdraw_delegator_reward',
                'begin_unbonding'           =>'begin_unbonding',
                'begin_redelegate'          =>'begin_redelegate',
                'delegate'                  =>'delegate'
            ],
            ['prompt'=>'','class' => 'form-control']),
    ];
    $columns[]= [
        'template'=>'{view}',
        'class' => 'yii\grid\ActionColumn'
    ];

    ?>

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
        'columns' => $columns
    ]); ?>

    <?php Pjax::end(); ?>

</div>
