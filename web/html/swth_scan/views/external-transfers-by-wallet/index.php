<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\ExternalTransfersByWallet;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ExternalTransfersByWalletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'External Transfers By Wallets');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="external-transfers-by-wallet-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <br>
    <br>

    <?php Pjax::begin(); ?>

    <?php

    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;


    $choice_values = ExternalTransfersByWallet::find()->select(['denom'])->distinct()->asArray()->all();
    $denoms = ArrayHelper::map($choice_values, 'denom', 'denom');

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
                'attribute'=>'in',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                /*'value'=>function($data){
                    return $data->value/pow(10,8);
                }*/
            ],
            [
                'attribute'=>'out',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                /*'value'=>function($data){
                    return $data->value/pow(10,8);
                }*/
            ],
            [
                'attribute'=>'balance',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                /*'value'=>function($data){
                    return $data->value/pow(10,8);
                }*/
            ],
            [
                'attribute'=> 'denom',
                'filter' => Html::dropDownList($searchModel->formName() . '[denom]', $searchModel->denom, $denoms,['prompt'=>'','class' => 'form-control']),
                'format'=>'raw'
            ],
            [
                'attribute'=>'last_in',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    if(empty($data->last_in)) return null;
                    $date = new \DateTime($data->last_in,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'last_out',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    if(empty($data->last_out)) return null;
                    $date = new \DateTime($data->last_out,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            'count',
            //'transaction_hash',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
