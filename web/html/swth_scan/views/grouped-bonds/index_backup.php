<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GroupedBondsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Grouped Bonds');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grouped-bonds-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php Pjax::begin(); ?>

    <?php

    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;

    ?>

    <p style="color:#a6a6a6;float:right">Timezone: <?=$timezone?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
                'attribute'=>'value',
                'format'=>['decimal',2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'value'=>function($data){
                    return $data->value/pow(10,8);
                }
            ],
            [
                'attribute'=>'first_date',
                'label'=>'first',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->first_date,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'last_date',
                'label'=>'last',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->last_date,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
                }
            ],
            [
                'attribute'=>'bonds_count',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='/bonds?BondsSearch[wallet]={$data->wallet}'>{$data->bonds_count}</a>";
                }
                //'filter'=>false
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
