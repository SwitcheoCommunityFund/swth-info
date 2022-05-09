<?php

use yii\web\View;

$this->registerCssFile('/css/loaders.css');
$js=<<<JS

    var jsoneditor=[];

    $(document).on('click','.get_swth',function()
    {
        if($('#log_loader').find('.loader').length>0) return console.log('cannot retry faucet until loading');
        //https://cdn.lowgif.com/full/cfa810430e2a3602-concerving-natural-resources.gif
        
        var wallet = $('#wallet').val();
        var amount = $('#amount').val();
        
        $.ajax({
            url:'/site/get-faucet',
            method:'POST',
            data:{wallet:wallet,amount:amount},
            success:function(res){
                if(res.status=='success'){
                    $('#log').append($('<h3>').attr('class','succ_msg').text('Tokens sent'));
                } else {
                    $('#log').append($('<h3>').attr('class','fail_msg').text(res.fails[0]));
                }
                $('#log_loader').html('');
            },
            beforeSend:function(){
                $('#log').html('');
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

<style>

    .succ_msg {
        color: #10a800
    }
    .fail_msg {
        color: #e0a200;
    }
    #log_loader {
        padding: 0px;
        /*margin-top: -25px;*/
    }
    .unfocused {
        color: #bdbdbd;
        font-size: 16px;
    }
</style>

<h2>Faucet for those who need spare SWTH</h2>
<h4 class="unfocused">(onboarding community, deposits, initial trading (no SWTH left or unable to cancel), those staked it all, etc.)</h4>


<br>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <label for="wallet">Enter your swth1 wallet below</label>
    </div>
    <div class="col-md-4">
        <input id="wallet" name="wallet" class="form-control form-group">
    </div>

    <div class="col-md-2">
        <select class="form-control form-group" id="amount" name="amount" >
            <option value="1">1SWTH</option>
            <option value="2" selected>2SWTH</option>
            <!--<option value="3">3SWTH</option>-->
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-success form-group get_swth">GET</button>
    </div>
    <div class="col-md-12">
        <div id="log_loader"></div>
    </div>
    <div class="col-md-12 row">
        <div class="col-md-6">
            <div class="panel" id="log" style="min-height:100px; height:100px; padding:10px"></div>
        </div>
    </div>
    <div class="col-md-12 row">
        <div class="col-md-6 unfocused">
            <p>If you would like to add some SWTH to the distributing address, you can send tokens to  <a href="https://switcheo.org/account/swth1p4g4va34a4ea55x0cc4wlsuuwaeppnfv8ane4r?net=main">swth1p4g4va34a4ea55x0cc4wlsuuwaeppnfv8ane4r</a>, to support the Community Fund please donate to <a href="https://switcheo.org/account/swth1vxdnh987wa7l88qlamk899s85fun7n2zr0ppuk?net=main">swth1vxdnh987wa7l88qlamk899s85fun7n2zr0ppuk</a>.</p>
            <br>
            <p>P.s.: if you have more than 2 SWTH available, already used the tool less than 24 hours ago or entered an incorrect wallet address you will not be able to receive the tokens.</p>
        </div>
    </div>
</div>