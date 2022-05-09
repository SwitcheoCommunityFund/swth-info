<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Bonds */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bonds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->registerCssFile('/js/jsoneditor/dist/jsoneditor.css');
$this->registerJsFile('/js/jsoneditor/dist/jsoneditor.js', ['position' => yii\web\View::POS_END]);



$transaction_details = <<< JS

    var address = 'https://tradescan.switcheo.org/'; 

    $(function(){
        $.get(address + 'get_transactions',{after_id:{$model->id}-1,limit:1},function(tr)
        {
            jsonReaderPreparation('transaction_messages',tr[0].msg);
            $.get(address + 'get_tx_log',{hash:tr[0].hash},function(tr_log)
            {
                console.log(tr_log);
                tr_log = JSON.parse(tr_log.raw_log);
                console.log(tr_log);
                jsonReaderPreparation('transaction_details',tr_log);
            }); 
        });
    });
    
    function jsonReaderPreparation(name,value=null)
    {
        var options ={
            'mode': 'tree',
            'modes': ['form', 'tree', 'view']
        };
        //var container = document.getElementById('add_params');
        jsoneditor[name] = new JSONEditor($('#'+name)[0],options);
        if(value!=null){
            console.log(typeof value);
            if(typeof value == 'string'){
                jsoneditor[name].set(JSON.parse(value));
            } else jsoneditor[name].set(value);
            jsoneditor[name].expandAll();
        }
        //console.log(jsoneditor);
    }

JS;

$this->registerJs($transaction_details,View::POS_READY);

?>
<script>
    var jsoneditor=[];
</script>

<div class="bonds-view">

    <h1>Transaction <?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'wallet',
            'value',
            'date',
            'denom',
        ],
    ]) ?>

    <h1>Messages details:</h1>
    <div class="panel" id="transaction_messages" style="min-height:300px; height:400px">

    </div>

    <h1>Transaction log:</h1>
    <div class="panel" id="transaction_details" style="min-height:600px; height:600px">

    </div>
</div>
