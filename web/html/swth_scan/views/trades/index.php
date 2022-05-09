<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\helpers\ViewCommon;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TradesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Trades');
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previousWallet = 'empty';

    //$(document).on('focusout','#wallet_search',function(){
    //    insertGeParam($(this).attr('name'), $(this).val(),false); 
    //});

    $(document).on('click change','#precise_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });

    $(document).on('click','#excel_export',function()
    {
       var wallet = findGetParam('TradesSearch[wallet]');
       var input_wallet = $('#wallet_search').val();
       //console.log(input_wallet);
       if(input_wallet!=null && input_wallet!='') {
           insertGeParam('TradesSearch[wallet]',input_wallet,false); 
           wallet=input_wallet;
       } 
       if(wallet==null || wallet==''){
           alert('Please add your wallet name into the search field');
       } else {
           location.href = 
                location.origin 
                + '/trades'
                + '/excel-export' 
                + location.search;
       }
    });
    
    
    $(document).on('pjax:send', function() {
      reloadCharts();    
    });
    
    $(document).on('pjax:complete', function() {
    });
    
    function reloadCharts()
    {
        var wallet = findGetParam('TradesSearch[wallet]');
        if(wallet==previousWallet) return;
        loadDailyChart({wallet:wallet});
        loadMonthlyChart({wallet:wallet});
        previousWallet = wallet; 
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
    .export_group {
        margin-bottom: 35px;
    }
</style>

<div class="trades-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>

    <?php
    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;
    ?>

    <div class="row" id="charts_placement">
        <?php
            echo $this->render('//charts/day-month',[
                'charts-controller'=>'trades',
                'show_series'=>['Total USD'],
                'params'=>['wallet'=>@$_GET['TradesSearch']['wallet']]
            ]);
        ?>
    </div>

    <br>
    <br>

    <div class="row">
        <div class="col-md-8 switcher_group">
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
            <p style="color:#a6a6a6">Timezone: <?=$timezone?></p>
        </div>
    </div>

    <br>



    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="col-md-12 export_group">
        <div class="col-md-8"><?= $form->field($searchModel, 'wallet',[
                'inputOptions'=>[
                    'id'=>'wallet_search',
                    'class'=>'form-control',
                    'placeholder' => "Search by Wallet"
                ]
            ])->label(false) ?></div>
        <div class="col-md-4">
            <label>
            <span class="btn btn-primary btn-m" style="float:right" id="excel_export">
                    <i class="glyphicon glyphicon-download-alt"></i>&nbsp;Excel Export
                </span>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::begin(['timeout'=>5000]); ?>

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
            //'taker_id',
            //'taker_address',
            //'taker_fee_amount',
            //'taker_fee_denom',
            //'taker_side',
            //'maker_id',
            [
                'attribute'=>'maker_address',
                'format'=>'raw',
                'value'=>function($data){
                    $maker_address = '...'.substr($data->maker_address,-6);
                    return "<a href='https://switcheo.org/account/{$data->maker_address}?net=main' title='{$data->maker_address}' target='_blank'>{$maker_address}</a>";
                }
            ],
            [
                'attribute'=>'taker_address',
                'format'=>'raw',
                'value'=>function($data){
                    $taker_address = '...'.substr($data->taker_address,-6);
                    return "<a href='https://switcheo.org/account/{$data->taker_address}?net=main' title='{$data->taker_address}' target='_blank'>{$taker_address}</a>";
                }
            ],
            [
                'attribute'=>'block_created_at',
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
                    $date = new \DateTime($data->block_created_at,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'maker_fee_amount',
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data)use($denoms){
                    return $data->maker_fee_amount==null?null:$data->maker_fee_amount/pow(10,(int)@$denoms[$data->maker_fee_denom]['decimals']);
                }
            ],
            'maker_fee_denom',
            [
                'attribute'=>'maker_side',
                'filter' => Html::dropDownList($searchModel->formName() . '[maker_side]', $searchModel->maker_side,['sell'=>'sell','buy'=>'buy'],['prompt'=>'','class' => 'form-control']),
            ],
            [
                'attribute'=>'market',
                'value'=>'m.display_name'
            ],
            [
                'attribute'=>'price',
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data)use($denoms){
                    return $data->price==null?null:$data->price;
                }
            ],
            [
                'label'=>'Price Denom',
                'attribute'=>'priceQuote',
                'value'=>'m.quote',
            ],
            [
                'attribute'=>'quantity',
                'format'=>['decimal',$searchModel->varPreciseValues?4:0],
                'contentOptions'=>['style' => 'text-align: right;'],
            ],
            [
                'attribute'=>'amount',
                'label'=>'Total',
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data)use($denoms){
                    return $data->price * $data->quantity;
                }
            ],
            [
                'attribute'=>'amountUsd',
                'label'=>'Total USD',
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->price * $data->quantity * $data->usdPrice;
                }
            ],
            //'liquidation:ntext',
            //'block_height',

            [
                'template'=>'{view}',
                'class' => 'yii\grid\ActionColumn'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
