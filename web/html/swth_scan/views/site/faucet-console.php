<?php

use yii\web\View;

$this->registerCssFile('/js/jsoneditor/dist/jsoneditor.css');
$this->registerJsFile('/js/jsoneditor/dist/jsoneditor.js', ['position' => yii\web\View::POS_END]);
$this->registerCssFile('/css/loaders.css');
$js=<<<JS

    var jsoneditor=[];

    jsonReaderPreparation('log',{});

    $(document).on('click','.get_swth',function()
    {
        var wallet = $('#wallet').val();
        
        $.ajax({
            url:'/site/get-faucet-console',
            method:'POST',
            data:{wallet:wallet},
            success:function(res){
                jsoneditor['log'].set(res);
                jsoneditor['log'].expandAll();
                $('#log_loader').html('');
            },
            beforeSend:function(){
                $('#log_loader').html('<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');
            }
        });
        
    });

    function jsonReaderPreparation(name,value=null)
    {
        var options ={
            'mode': 'view',
            'modes': ['view','code']
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

$this->registerJs($js,View::POS_READY);


?>

<br>

<h2>Faucet console</h2>

<br><br>

<label for="wallet">Put your wallet</label>
<input id="wallet" name="wallet" class="form-control">
<button class="btn btn-success get_swth">get swth</button><div id="log_loader"></div>
<div class="panel" id="log" style="min-height:600px; height:600px">

</div>
