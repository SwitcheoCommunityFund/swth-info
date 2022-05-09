<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;
use app\helpers\ViewCommon;
/* @var $this yii\web\View */
/* @var $searchModel app\models\RewardsByWalletSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Rewards By Wallets');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS
    
    $(document).on('click change','#empty_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });

    $(document).on('click change','#precise_switcher',function(){
       insertGeParam($(this).attr('name'), $(this).is(":checked")?1:0, true); 
    });
    
    
    $(document).on('pjax:send', function() {
      $('#loading').show()
    });
    $(document).on('pjax:complete', function() {
      $('#loading').hide()
    });

JS;


$denoms = ViewCommon::getDenoms();

$this->registerJs($js,View::POS_READY);


?>

<style>
    .switcher_group {
        margin-bottom:15px
    }
</style>
<div class="rewards-by-wallet-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <br>

    <?php

        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

    ?>

    <div class="row">
        <div class="col-md-8 switcher_group">
            <div class="col-md-12">
                <div class="col-md-3">
                    <label>Empty values </label>
                </div>
                <div class="col-md-9">
                    <input id='empty_switcher'
                           data-size="xs"
                           type='checkbox'
                           data-width="70"
                           data-on="included"
                           data-onstyle="success"
                           data-off="excluded"
                           data-offstyle="info"
                           data-toggle='toggle'
                           name="<?= $searchModel->formName().'[varEmptyValues]'?>"
                        <?= $searchModel->varEmptyValues?'checked':null; ?>
                    />
                </div>
            </div>
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
            <p style="color:#a6a6a6" class="pull-right">Timezone: <?=$timezone?></p>
        </div>
    </div>

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

            [
                'attribute'=>'wallet',
                'format'=>'raw',
                'value'=>function($data){
                    return "<a href='https://switcheo.org/account/{$data->wallet}?net=main' target='_blank'>{$data->wallet}</a>";
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
                'format'=>['decimal',$searchModel->varPreciseValues?8:2],
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
                    $wallet = urlencode($data->wallet);
                    $denom = $data->denom==null?'null':urlencode($data->denom);
                    $link = "?RewardsSearch[wallet]={$wallet}&RewardsSearch[denom]={$denom}";
                    //$link .= "&RewardsSearch[validator]={$data->validator}";
                    return "<a data-pjax='0'  href='/rewards/{$link}'>{$data->rewards_count}</a>";
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
