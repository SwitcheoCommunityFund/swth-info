<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AirdropLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Airdrop Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="airdrop-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;
    ?>
    <div class="row col-md-12">
        <div class="col-md-3 pull-right">
            <p style="color:#a6a6a6">Timezone: <?=$timezone?></p>
        </div>
    </div>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute'=>'tx_id',
                'format'=>'raw',
                'value'=>function($data){
                    $tx_id = substr($data->tx_id,0,6).'...'.substr($data->tx_id,-6);
                    return "<a href='https://etherscan.io/tx/{$data->tx_id}' title='{$data->tx_id}' target='_blank'>{$tx_id}</a>";
                }
            ],
            [
                'attribute'=>'air_time',
                'format'=>'raw',
                /*'filter'=>DateRangePicker::widget([
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
                ]),*/
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->air_time,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i:s');
                }
            ],
            'amount',
            [
                'attribute'=>'wallet',
                'format'=>'raw',
                'value'=>function($data){
                    $wallet = substr($data->wallet,0,5).'...'.substr($data->wallet,-6);
                    return "<a href='https://switcheo.org/account/{$data->wallet}?net=main' title='{$data->wallet}' target='_blank'>{$wallet}</a>";
                }
            ],
            'states.state_code',
            //'log:ntext',

            [
                'template'=>'{view}',
                'class' => 'yii\grid\ActionColumn'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
