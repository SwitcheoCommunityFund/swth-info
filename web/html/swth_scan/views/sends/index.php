<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\helpers\ViewCommon;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SendsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Sends');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$denoms = ViewCommon::getDenoms();

$js = <<< JS
    $(document).on('click change','#precise_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });
JS;
$this->registerJs($js,View::POS_READY);

?>
<div class="sends-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <br>

    <?php
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;
    ?>

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]);
    ?>
    <div class="col-md-12">
        <div class="col-md-2">
            <label>Precise values </label>
        </div>
        <div class="col-md-10" >
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
    <div class="col-md-12">
        <div class="col-md-8"><?= $form->field($searchModel, 'wallet',[
                'inputOptions'=>[
                    'id'=>'wallet_search',
                    'class'=>'form-control',
                    'placeholder' => "Search by Wallet"
                ]
            ])->label(false) ?></div>
        <div class="col-md-4">
            <p style="color:#a6a6a6" class="pull-right">Timezone: <?=$timezone?></p>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <br>

    <?php Pjax::begin(); ?>

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
                'attribute'=>'tr_hash',
                'format'=>'raw',
                'value'=>function($data){
                    $tr_hash = substr($data->tr_hash,0,6).'...'.substr($data->tr_hash,-6);
                    return "<a href='https://switcheo.org/transaction/{$data->tr_hash}?net=main' title='{$data->tr_hash}' target='_blank'>{$tr_hash}</a>";
                }
            ],
            [
                'attribute'=>'from',
                'format'=>'raw',
                'value'=>function($data){
                    $from = '...'.substr($data->from,-6);
                    return "<a href='https://switcheo.org/account/{$data->from}?net=main' title='{$data->from}' target='_blank'>{$from}</a>";
                }
            ],
            [
                'attribute'=>'to',
                'format'=>'raw',
                'value'=>function($data){
                    $to = '...'.substr($data->to,-6);
                    return "<a href='https://switcheo.org/account/{$data->to}?net=main' title='{$data->to}' target='_blank'>{$to}</a>";
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
                'attribute'=>'amount',
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->amount==null?null:$data->amount/pow(10,(int)@$data->token->decimals);
                }
            ],
            [
                'attribute'=>'denom',
                'filter' => Html::dropDownList($searchModel->formName() . '[denom]', $searchModel->denom, $denoms,['prompt'=>'','class' => 'form-control']),
                'format'=>'raw'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
