<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use app\helpers\ViewCommon;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SummarySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Summaries');
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    var previousWallet = 'empty';


    $(document).on('click change','#precise_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });

JS;


$this->registerJs($js,View::POS_READY);

$tokens = ViewCommon::getTokens();



?>
<style>
    .bc-name {
        color: #006a00;
        padding-right: 4px;
        /*float:left;*/
        font-weight: 200;
        min-width: 60px;
    }
    .bc-sum {
        float:right;
    }
</style>
<div class="summary-index">

    <h1><?= Html::encode($this->title) ?></h1>

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
    </div>

    <br>
    <br>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
                'attribute'=>'staked_value',
                'format'=>['decimal',@$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'filter'=>false,
                'value'=>function($data){
                    return $data->staked_value==null?null:$data->staked_value/pow(10,8);
                }
            ],
            [
                'attribute'=>'wait_unbonding_value',
                'format'=>['decimal',@$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'filter'=>false,
                'value'=>function($data){
                    return $data->wait_unbonding_value==null?null:$data->wait_unbonding_value/pow(10,8);
                }
            ],
            [
                'attribute'=>'unbonding_value',
                'format'=>['decimal',@$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'filter'=>false,
                'value'=>function($data){
                    return $data->unbonding_value==null?null:$data->unbonding_value/pow(10,8);
                }
            ],
            /*[
                'attribute'=>'rewards_value',
                'format'=>['decimal',@$searchModel->varPreciseValues?8:2],
                'contentOptions'=>['style' => 'text-align: right;'],
                'filter'=>false,
                'value'=>function($data){
                    return $data->rewards_value==null?null:$data->rewards_value/pow(10,8);
                }
            ],*/
            [
                'attribute'=>'rewards_value',
                'format'=>'raw',
                'filter'=>false,
                'value'=>function($data) use ($tokens,$searchModel){
                    if(!is_array($data->rewards_value)) return null;
                    $tab='<table>';
                    foreach ($data->rewards_value as $bc=>$val)
                    {
                        $raw=$val;
                        if(empty($val)) continue;
                        $decimals=(int)@$tokens[$bc]['decimals'];
                        $val = $val/pow(10,$decimals);
                        $prep_val = Yii::$app->formatter->format($val, ['decimal', @$searchModel->varPreciseValues?8:2]);
                        $tab .= "<tr><td class='bc-name'>$bc:</td><td class='bc-sum' data-decimals='$decimals' data-val='$raw'>$prep_val</td></tr>";
                    }

                    return $tab=='<table>'?'':$tab.'</table>';
                }
            ],
            [
                'attribute'=>'external_in',
                'format'=>'raw',
                'filter'=>false,
                'value'=>function($data) use ($searchModel){
                    if(!is_array($data->external_in)) return null;
                    $tab='<table>';
                    foreach ($data->external_in as $bc=>$val)
                    {
                        if(empty($val)) continue;
                        $prep_val = Yii::$app->formatter->format($val, ['decimal', @$searchModel->varPreciseValues?8:2]);
                        $tab .= "<tr><td class='bc-name'>$bc:</td><td class='bc-sum'>$prep_val</td></tr>";
                    }

                    return $tab=='<table>'?'':$tab.'</table>';
                }
            ],
            [
                'attribute'=>'external_out',
                'format'=>'raw',
                'filter'=>false,
                'value'=>function($data) use ($searchModel){
                    if(!is_array($data->external_out)) return null;
                    $tab='<table>';
                    foreach ($data->external_out as $bc=>$val)
                    {
                        if(empty($val)) continue;
                        $prep_val = Yii::$app->formatter->format($val, ['decimal', @$searchModel->varPreciseValues?8:2]);
                        $tab .= "<tr><td class='bc-name'>$bc:</td><td class='bc-sum'>$prep_val</td></tr>";
                    }
                    return $tab=='<table>'?'':$tab.'</table>';
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
