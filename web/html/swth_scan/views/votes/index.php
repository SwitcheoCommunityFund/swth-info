<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $searchModel app\models\VotesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Votes');
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previousProposal = 'empty';

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
        var proposal_id = findGetParam('VotesSearch[proposal_id]');
        if(!!!proposal_id) proposal_id = '{$searchModel->proposal_id}';
        if(proposal_id==previousProposal) return;
        $.ajax({
            url:'/votes/charts',
            method:'POST',
            data:{proposal_id:proposal_id},
            success:function(data){
                $('#charts_placement').html(data);
                previousProposal = proposal_id;
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


<div class="votes-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php

        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

    ?>

    <div class="col-md-6" id="charts_placement"></div>

    <?php Pjax::begin(); ?>
    <div class="col-md-6">
        <?php  echo $this->render('_search_proposal', ['model' => $searchModel]); ?>
    </div>

    <br>

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

            'id',
            //'tr_hash',
            'proposal_id',
            [
                'attribute'=>'proposalTitle',
                'value'=>function($data){
                    return @$data->p->title;
                }
            ],
            [
                'attribute'=>'voter',
                'format'=>'raw',
                'value'=>function($data){
                    if(@$data->v->name){
                        return "<a href='https://switcheo.org/validator/{$data->v->address}?net=main' target='_blank'>{$data->v->name}</a>";
                    } else return  "<a href='https://switcheo.org/account/{$data->voter}?net=main' target='_blank'>{$data->voter}</a>";
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
            'option',
            [
                'attribute'=>'date',
                'format'=>'raw',
                'value'=>function($data) use ($timezone){
                    $date = new \DateTime($data->date,(new \DateTimeZone('UTC')));
                    $date->setTimezone((new \DateTimeZone($timezone)));
                    return $date->format('d.m.Y&\n\b\s\p;H:i');
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
