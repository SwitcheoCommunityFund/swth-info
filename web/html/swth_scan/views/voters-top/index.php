<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\VotersTopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Voters Top');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voters-top-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <br />
    <br />

    <?php

    $session = Yii::$app->session;
    $timezone = $session['timezone_name'];
    $timezone = empty($timezone)?'UTC':$timezone;

    ?>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary'=>"<div class='col-md-12' style='padding: 0px'><div class='col-md-6 col-xs-6'>Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> items.</div><div class='col-md-6 col-xs-6'><p style=\"color:#a6a6a6; float:right;\">TZ: $timezone</p></div></div>",
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
                'attribute'=>'voter',
                'format'=>'raw',
                'value'=>function($data){
                    if(@$data->v->name){
                        return "<a href='https://switcheo.org/validator/{$data->v->address}?net=main' target='_blank'>{$data->v->name}</a>";
                    } else return  "<a href='https://switcheo.org/account/{$data->voter}?net=main' target='_blank'>{$data->voter}</a>";
                }
            ],
            'proposals',
            [
                'attribute' => 'votes',
                'format'=>'raw',
                'value'=>function($data) use ($searchModel){
                    $sm = 'VotesSearch';
                    return "<a href='/votes?{$sm}[voter]={$data->voter}&{$sm}[voterType]=&{$sm}[proposal_id]=' data-pjax='0' target='_blank'>{$data->votes}</a>";
                }
            ],
            [
                'attribute'=>'voterType',
                'format'=>'raw',
                'filter' => Html::dropDownList($searchModel->formName() . '[voterType]', $searchModel->voterType, ['validator'=>'validator','wallet'=>'wallet'],['prompt'=>'','class' => 'form-control']),
                'value'=>function($data){
                    return @$data->v->name?'validator':'wallet';
                }
            ],
            [
                'attribute'=>'date_create',
                'label'=>'Account Created',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    if(@$data->v->date_create) {
                        $date = new \DateTime($data->v->date_create, (new \DateTimeZone('UTC')));
                        $date->setTimezone((new \DateTimeZone($timezone)));
                        return $date->format('d.m.Y&\n\b\s\p;H:i');
                    } else return null;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
