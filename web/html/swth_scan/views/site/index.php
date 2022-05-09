<?php



$this->registerCssFile('/css/loaders.css');

$loader_pattern = '<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>';

$tokens_js = json_encode($tokens);

prepareTokens($tokens);

function prepareTokens(&$tokens){$prep=[];foreach ($tokens as $token){$prep[$token['denom']]=$token;} $tokens=$prep;}

function fetchBcValues($values,$tokens,$usedecimals=true)
{
    $tab = '<table class="bc_table">';
    foreach ($values as $value){
        $group = @$value['denom'];
        $decimal = @$tokens[$group]?$tokens[$group]['decimals']:8;
        $decimal = !$usedecimals?0:$decimal;
        $prep_val = Yii::$app->formatter->format($value['value']/pow(10,$decimal), ['decimal', 2]);
        $tab .= "<tr>
                    <td class='bc_name'><img class='token_img' src='/img/tokens/{$tokens[$group]['image']}'>{$group}</td>
                    <td class='metric' data-decimal='$decimal'>{$prep_val}</td>
                </tr>";
    }
    return $tab . '</table>';
}

/* @var $this yii\web\View */




$this->title = 'Switcheo scan';

$js = <<< JS

    var tokens = {$tokens_js};
    tokens = tokens.reduce((a,v)=>Object.assign(a,{[v.denom]:v}),{});
    
    //console.log(tokens);
    
    updateWithdrawalsDeposits();
    updateSwthDevFund();
    updateRewards();
    updateSwthTotalSupply();
    updateTradingSummary();
    updateTransactionsCountChart();
    
    var I15 = setInterval(function(){
        updateSwthDevFund();
        updateSwthTotalSupply();
    },15000); 

    var I350 = setInterval(function(){
        /*$.get('https://tradescan.switcheo.org/distribution/community_pool',{},function(data){
            
        });*/
    },350000); 
    
    function updateWithdrawalsDeposits()
    {
        $.get('/site/withdrawals-deposits',{},function(data){
            $('#withdrawals').html(generateDenomDoubleTable(data.withdrawals,'denom','month','week',['Withdrawals','This Month','This Week'],false));
            $('#deposits').html(generateDenomDoubleTable(data.deposits,'denom','month','week',['Deposits','This Month','This Week'],false));
        });
    }
    
    function updateRewards()
    {
        $.get('/rewards/stats',{},function(data){
            $('#rewards').html(generateDenomDoubleTable(data.rewards,'denom','month','week',['','This Month','This Week'],true));
        });
    }
    
    function updateSwthTotalSupply(){
        if(!document.hidden){
            $.get('https://api.switcheo.network/v2/exchange/native_token_supply',{},function(data){
                if(typeof data !== 'undefined' && data!==null){
                    var swth_total_supply = data;
                    $('#switcheo-total-supply').html(generateDenomTable([{
                            denom : 'swth',
                            value : swth_total_supply,
                    }],'denom','value',false));
                    /*$.ajax({
                        url:'https://mainnet.api.neotube.io/api/asset',
                        data:'{"method":"getnep5info","params":["3e09e602eeeb401a2fec8e8ea137d59aae54a139"]}',
                        type:'json',
                        method:'POST',
                        success:function(data){
                            //console.log(parseFloat(data.result.amount),swth_total_supply,typeof data.result.amount, typeof swth_total_supply);
                            $('#switcheo-total-supply').html(generateDenomTable([{
                                    denom : 'swth',
                                    value : parseFloat(data.result.amount) + swth_total_supply,
                            }],'denom','value',false));
                        }
                    });*/
                } else {
                    clearInterval(I15);
                }
            });
        } else {
            console.log('page is closed now');
        }
    }
    
    function updateTradingSummary(){
        if(!document.hidden){
            $.get('/site/trading-summary',{},function(data){
                //console.log(data.trades_summary_usd);
               var tab = 
               /*$('<table>').attr('class','metric-table')
               .append(
                   $('<tr>')
                   .append($('<td class="field">24 Hours:</td>'))
                   .append($('<td>').html(generateDenomTable(data.trades_summary,'denom','trade_24h_sum',false,true)))
               )
               .append(
                   $('<tr>').append($('<td colspan=2 align=right>')
                   .css({padding:'3px 0px 9px 0px','font-weight':300,color:'#232323'})
                   .text(formatValue(parseFloat(data.trades_summary_usd.trade_24h_sum_usd))+' USD'))
               )
               .append(
                   $('<tr>')
                   .append($('<td class="field">This month:</td>'))
                   .append($('<td>').html(generateDenomTable(data.trades_summary,'denom','trade_month_sum',false,true)))
               )
               .append(
                   $('<tr>').append($('<td colspan=2 align=right>')
                   .css({padding:'3px 0px 9px 0px','font-weight':300,color:'#232323'})
                   .text(formatValue(parseFloat(data.trades_summary_usd.trade_month_sum_usd))+' USD'))
               )
               .append(
                   $('<tr>')
                   .append($('<td class="field">Total:</td>'))
                   .append($('<td>').html(generateDenomTable(data.trades_summary,'denom','trade_sum',false,true)))
               )
               .append(
                   $('<tr>').append($('<td colspan=2 align=right>')
                   .css({padding:'3px 0px 9px 0px','font-weight':300,color:'#232323'})
                   .text(formatValue(parseFloat(data.trades_summary_usd.trade_sum_usd))+' USD'))
               );*/
               $('<table>').attr('class','metric-table')
               .append(
                   $('<tr>')
                   .append($('<td class="field">24 Hours:</td>'))
                   .append($('<td colspan=2 align=right>')
                       .css({padding:'3px 0px 9px 0px','font-weight':400})
                       .text(formatValue(parseFloat(data.trades_summary_usd.trade_24h_sum_usd))+' USD')
                   )
               )
               .append(
                   $('<tr>')
                   .append($('<td class="field">This month:</td>'))
                   .append($('<td colspan=2 align=right>')
                       .css({padding:'3px 0px 9px 0px','font-weight':400})
                       .text(formatValue(parseFloat(data.trades_summary_usd.trade_month_sum_usd))+' USD'))
               )
               .append(
                   $('<tr>')
                   .append($('<td class="field">Total:</td>'))
                   .append($('<td colspan=2 align=right>')
                       .css({padding:'3px 0px 9px 0px','font-weight':400})
                       .text(formatValue(parseFloat(data.trades_summary_usd.trade_sum_usd))+' USD'))
               )
               $('#trading-summary').html(tab); 
            });
        } else {
            console.log('page is closed now');
        }
    }
    
    function updateSwthDevFund(){
        if(!document.hidden){
            $.get('https://tradescan.switcheo.org/distribution/community_pool',{},function(data){
                if(typeof data.result !== 'undefined'){
                    $('#switcheo-development-fund').html(generateDenomTable(data.result,'denom','amount'))
                } else {
                    clearInterval(I15);
                }
            });
        } else {
            console.log('page is closed now');
        }
    }
    
    function updateTransactionsCountChart(){
        if(!document.hidden){
            $.get('/transactions-count/panel-chart',{},function(data){
                $('#switcheo-transactions-count').html(data).css({height:'150px',margin:'0px -31px'});
            });
        }
    }
    
    function generateDenomTable(data,d='denom',v='value',enc=true,prsf=false)
    {
        var tab = $('<table>').attr({class:'bc_table'});  
        for(var i in data){
            var row = data[i];
            var value = row[v];
            if(prsf){
                var value = parseFloat(row[v]);
            }
            if(enc){
                var value = formatDenomValue(value,row[d]);
            } else var value = formatValue(value);
            var token_props = checkTokenProps(row[d]);
            var denom = cutByLen(row[d],6);
            var tr = $("<tr>")
                .append($('<td>').attr({class:'bc_name'}).append($('<img>').attr({class:'token_img',src:'/img/tokens/'+token_props.image})).append(denom))
                .append($('<td>').attr({class:'metric'}).text(value));
            tab.append(tr);                    
        }
        return tab;
    }
    
    function generateDenomDoubleTable(data,d='denom',v='month',v2='week',header=['',v1,v2],enc=true,prsf=false)
    {
        var tab = $('<table>').attr({class:'bc_table'});
        if(Array.isArray(header)){
            tab.append($('<thead>').html('<tr>' +
                                            '<td align="center" class="field">'+header[0]+'</td>' +
                                            '<td align="center" class="field">'+header[1]+'</td>' +
                                            '<td align="center" class="field">'+header[2]+'</td>' +
                                         '</tr>'));
        } else {
            tab.append(header);
        }
        var tbody = $('<tbody>');
        for(var i in data){
            var row = data[i];
            var value1 = row[v];
            var value2 = row[v2];
            if(prsf){
                var value1 = parseFloat(row[v]);
                var value2 = parseFloat(row[v2]);
            }
            if(enc){
                var value1 = formatDenomValue(value1,row[d]);
                var value2 = formatDenomValue(value2,row[d]);
            } else {
                var value1 = formatValue(value1);
                var value2 = formatValue(value2);
            }
            var token_props = checkTokenProps(row[d]);
            var denom = cutByLen(row[d],6);
            var tr = $("<tr>")
                .append($('<td>').attr({class:'bc_name'}).append($('<img>').attr({class:'token_img',src:'/img/tokens/'+token_props.image})).append(denom))
                .append($('<td>').attr({align:'right'}).text(value1))
                .append($('<td>').attr({align:'right'}).text(value2));
            tbody.append(tr);                    
        }
        return tab.append(tbody);
    }
    
    function formatDenomValue(val,denom)
    {
        var value = parseFloat(val);
        value = value/Math.pow(10,checkDecimals(denom));
        return formatValue(value);
    }
    
    function checkDecimals(denom)
    {
        return (typeof tokens[denom]=='undefined')?8:tokens[denom].decimals;
    }
    
    function checkTokenProps(denom){
        return (typeof tokens[denom]=='undefined')?{}:tokens[denom];
    }
    
    function formatValue(value){
        return (new Intl.NumberFormat('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
    }
    
    function cutByLen(str,len,end='..')
    {
        return str.length > len ? str.substring(0, len) + end : str;
    }
    
JS;

$this->registerJs($js,yii\web\View::POS_READY);


?>

<style>
    .panel {
        box-shadow: 0px 3px 6px -3px rgba(0,0,0,0.6);
        padding: 0px 15px 15px 15px;
    }
    .metric {
        float:right;
    }
    .metric-note{
        font-size: 12px;
        color: #a2a2a2;
    }
    .big-metric {
        text-align: center;
        font-size: 30px;
        font-weight: 200;
    }
    .metric-table>tbody>tr>td {
        padding-bottom: 10px;
    }
    .metric-table{
        width:100%
    }
    .field {
        color:green;
        padding-right:5px;
        font-weight: 200;
        vertical-align: top;
    }
    .bc_name {
        color: #007ad4;
        float:left;
        padding-right:5px;
        text-transform: uppercase;
    }
    .bc_table {
        width:100%;
    }
    .timezone {
        float:right;
        color:gray;
        text-align: right;
        padding-bottom:20px;
    }
    .gl_name{
        color: #337ab7
    }
    .loader {
        margin: auto;
        width:100px;
        margin-top: -35px;
        margin-bottom: -35px;
    }
    .token_img {
        max-width: 15px;
        margin-right: 2px;
        vertical-align: text-bottom;
        margin-bottom: 0px;
    }

    #trading-summary{

    }
</style>
<br>
<div class="site-index">
    <div class="row">
        <div class="col-md-12"><div class="col-md-4 timezone">Timezone:<?=$timezone?></div></div>
        <div class="col-md-4">
            <div class="panel">
                <h3><a href="/external-transfers">External Transfers</a></h3>
                <div id="withdrawals"><?=$loader_pattern?></div>
                <br>
                <div id="deposits"><?=$loader_pattern?></div>
            </div>
            <div class="panel">
                <h3><span class="gl_name">Switcheo Total Supply</span></h3>
                <div id="switcheo-total-supply"><?=$loader_pattern?></div>
            </div>
        </div>
        <div class="col-md-8" style="padding-right: 0px; padding-left: 0px;">
            <div class="col-md-6">
                <div class="panel">
                    <h3><a href="/trading" >Trading Volume</a></h3>
                    <div id="trading-summary"><?=$loader_pattern?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel">
                    <h3><a href="/stakes" >Staked</a></h3>
                    <table class="metric-table">
                        <tr>
                            <td class="field"><!--Total delegates  t-->This month:</td>
                            <td class="metric"><?=Yii::$app->formatter->format($delegates_this_month, ['decimal', 2])?></td>
                        </tr>
                        <tr>
                            <td class="field"><!--Total delegates  t-->This week:</td>
                            <td class="metric"><?=Yii::$app->formatter->format($delegates_this_week, ['decimal', 2])?></td>
                        </tr>
                        <tr>
                            <td class="field"><!--Total delegates  t-->Total:</td>
                            <td class="metric"><?=Yii::$app->formatter->format($delegates_total-$unbonding_total, ['decimal', 2])?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel">
                <h4><a href="/airdrop-log">Number of created Ethereum wallets</a></h4>
                <div class="big-metric">
                    <?= $ethereum_created_wallets ?>
                </div>
                <div class="metric-note">* Ledger uses existing wallets and excluded from count here</div>
            </div>
            <div class="panel">
                <h3><a href="/rewards">Rewards</a></h3>
                <div id="rewards"><?=$loader_pattern?></div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel">
                <h3><a href="/unstakes">Unstakes</a></h3>
                <table class="metric-table">
                    <tr>
                        <td class="field"><!--Total unbonded t-->This month:</td>
                        <td class="metric"><?=Yii::$app->formatter->format($unbonded_this_month, ['decimal', 2])?></td>
                    </tr>
                    <tr>
                        <td class="field"><!--Total unbonded t-->This week:</td>
                        <td class="metric"><?=Yii::$app->formatter->format($unbonded_this_week, ['decimal', 2])?></td>
                    </tr>
                    <tr>
                        <td class="field"><!--Total unbonding i-->In future:</td>
                        <td class="metric"><?=Yii::$app->formatter->format($unbonding_in_future, ['decimal', 2])?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel">
                <h3><a href="/transactions-count">Transactions Count</a></h3>
                <div id="switcheo-transactions-count"><?=$loader_pattern?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel">
                <h3><span class="gl_name">Switcheo Development Fund</span></h3>
                <div id="switcheo-development-fund"><?=$loader_pattern?></div>
            </div>
        </div>
    </div>
</div>



