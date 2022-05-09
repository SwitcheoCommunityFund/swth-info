<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\helpers\ViewCommon;
use app\models\ExternalTransfers;
use kartik\daterange\DateRangePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ExternalTransfersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'External Transfers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-transfers-index">



    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <br>

    <?php Pjax::begin(); ?>

    <?php

    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;


    $choice_values = ExternalTransfers::find()->select(['status'])->distinct()->asArray()->all();
    $statuses = ArrayHelper::map($choice_values, 'status', 'status');
    $denoms = ViewCommon::getDenoms();
    $blockchains = ViewCommon::getBlockchains();
    $transfer_types = ViewCommon::getTransferTypes();
    //var_dump($transfer_types);

    ?>

    <p style="color:#a6a6a6;float:right">Timezone: <?=$timezone?></p>

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

            [
                'attribute'=>'wallet',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->wallet}?net=main' target='_blank'>{$data->wallet}</a>";
                }
            ],
            [
                'attribute'=>'amount',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                /*'value'=>function($data){
                    return $data->value/pow(10,8);
                }*/
            ],
            /*[
                'attribute'=>'fee_amount',
                'contentOptions'=>['style' => 'text-align: right;'],
                'format'=>['decimal',2],
            ],*/

            [
                'attribute'=> 'blockchain',
                'filter' => Html::dropDownList($searchModel->formName() . '[blockchain]', $searchModel->blockchain, $blockchains,['prompt'=>'','class' => 'form-control']),
                'format'=>'raw'
            ],

            [
                'attribute'=>'denom',
                'filter' => Html::dropDownList($searchModel->formName() . '[denom]', $searchModel->denom, $denoms,['prompt'=>'','class' => 'form-control']),
                'format'=>'raw'
            ],
            [
                'attribute'=>'status',
                'filter' => Html::dropDownList($searchModel->formName() . '[status]', $searchModel->status, $statuses,['prompt'=>'','class' => 'form-control']),
                'format' => 'raw',
                'value' => function($data){
                    $c = ($data->status=='success')?'green':'gray';
                    return "<span style='color:$c'>" . $data->status . "</span>";
                }
            ],
            [
                'attribute'=>'timestamp',
                'format'=>'raw',
                'filter'=>DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'timeRange',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
                        'timePicker'=>true,
                        'timePickerIncrement'=>30,
                        'locale'=>[
                            'format'=>'d.m.Y H:i'
                        ]
                    ],
                    'options'=>['class'=>'form-control','autocomplete'=>"off"]
                ]),
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->timestamp,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            //'transaction_hash',

            [
                'attribute'=>'transfer_type',
                'filter' => Html::dropDownList($searchModel->formName() . '[transfer_type]', $searchModel->transfer_type, $transfer_types,['prompt'=>'','class' => 'form-control']),
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
