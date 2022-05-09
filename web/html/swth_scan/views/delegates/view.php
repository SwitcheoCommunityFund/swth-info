<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Delegates */

$this->title = $model->denom;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Delegates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


$this->registerCssFile('/js/jsoneditor/dist/jsoneditor.css');
$this->registerJsFile('/js/jsoneditor/dist/jsoneditor.js', ['position' => yii\web\View::POS_END]);

$transaction_details = <<< JS

    var address = 'https://tradescan.switcheo.org/'; 

    $(function(){
        
            $.get(address + 'get_tx_log',{hash:'{$model->tr_hash}'},function(tr_log)
            {
                console.log(tr_log);
                tr_log = JSON.parse(tr_log.raw_log);
                console.log(tr_log);
                jsonReaderPreparation('transaction_details',tr_log);
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

<div class="delegates-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'wallet',
            'validator',
            'value',
            'date',
            'denom',
            'tr_hash',
        ],
    ]) ?>

    <h1>Transaction log:</h1>
    <div class="panel" id="transaction_details" style="min-height:600px; height:600px">

    </div>

</div>
