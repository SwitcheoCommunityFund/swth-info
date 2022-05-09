<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\helpers\ViewCommon;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RewardsByValidatorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rewards By Validators');
$this->params['breadcrumbs'][] = $this->title;



$denoms = ViewCommon::getDenoms();

?>
<div class="rewards-by-validator-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php Pjax::begin(); ?>

    <?php
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;
    ?>

    <p style="color:#a6a6a6;float:right">Timezone: <?=$timezone?></p>

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

            [
                'attribute'=>'validatorName',
                'format'=>'raw',
                'value'=>function($data){
                    if(!@$data->v->name) return null;
                    return "<a href='https://switcheo.org/validator/{$data->validator}?net=main' target='_blank'>{$data->v->name}</a>";
                }
            ],
            [
                'attribute'=>'last_award',
                'label'=>'last_reward',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->last_award,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'value',
                'format'=>['decimal',2],
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
            [
                'attribute'=>'rewards_count',
                'format'=>'raw',
                'value'=>function($data){
                    $validator_name = urlencode(@$data->v->name);
                    $denom = $data->denom==null?'null':urlencode($data->denom);
                    $link = "?RewardsSearch[validatorName]={$validator_name}&RewardsSearch[denom]={$denom}";
                    //$link = "?RewardsSearch[validator]={$data->validator}";
                    //$link .= "&RewardsSearch[wallet]={$data->wallet}";
                    return "<a data-pjax='0' href='/rewards/{$link}'>{$data->rewards_count}</a>";
                }
            ],
            [
                'template'=>'{view}',
                'class' => 'yii\grid\ActionColumn'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
