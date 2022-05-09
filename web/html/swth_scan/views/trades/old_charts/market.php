<?php


$this->registerCssFile('/css/loaders.css');

$loader_pattern = '<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>';



$this->title = 'Trading list';

$this->registerCssFile('/css/loaders.css');
$this->registerCssFile('/js/bootstrap/bootstrap4-toggle.min.css');
$this->registerJsFile('/js/bootstrap/bootstrap4-toggle.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('/js/helpers/web.helper.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$js = <<< JS

    $(function(){
        loadCharts();    
    });
    
    function loadCharts()
    {
        
        $.ajax({
            url:'/trades/charts',
            method:'POST',
            success:function(data){
                $('#charts_placement').html(data);
            },
            beforeSend: function(){ $('#charts_placement').html('<div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>'); } 
        });
    }

JS;


$this->registerJs($js,yii\web\View::POS_READY);


?>

<style>
    .panel {
        box-shadow: 0px 3px 6px -3px rgba(0,0,0,0.6);
        padding: 0px 15px 15px 15px;
        overflow: hidden;
    }
    .metric {
        /*float:right;*/
        text-align: right;
    }
    .metric_name {
        color: #bebebe
    }

    .metric-table>tbody>tr>td {
        padding-bottom: 10px;
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

    .market_name {
        text-transform: uppercase;
        font-weight: 200;
        font-size: 20px;
        margin-bottom: 13px;
        color: #0000008c;
    }

    .period_name {
        margin-top: 9px;
    }

    .denom{
        padding-left:4px;
        text-transform: uppercase;
        color: #007bff;
    }

    .title {
        margin-bottom: 30px;
    }

    #charts_placement {
        margin-bottom: 20px;
        min-height: 310px;
    }

    .focusout {
        opacity: 0.4;
    }

    .summaries {
        overflow-x: auto;
    }

    .summary_line>td {
        vertical-align: baseline;
    }

</style>

<div class="col-md-12 title">
    <h2>Trading summary</h2>
</div>


<div class="col-md-12" id="charts_placement"></div>

<?php


echo "<script>console.log('".json_encode($trades_summary)."');</script>";

$total_usd = 0;
$total_24h_usd = 0;
$total_month_usd = 0;

$total_line='';
$total_24h_line='';
$total_month_line='';
$k=1;

foreach ($trades_summary as $summary)
{
    $pls = ($summary['count'] > $k)?'+':null;

    $total_usd       += $summary['trade_sum_usd'];
    $total_24h_usd   += $summary['trade_24h_sum_usd'];
    $total_month_usd += $summary['trade_month_sum_usd'];

    $total_line       .= '<td align="right" class="focusout">' . ($k==1?'&nbsp;(':'') . prettyNum($summary['trade_sum'])       . '<span class="denom">'.trim($summary['denom'],'1') . "</span>" . ($pls==null?')':'') . "&nbsp;{$pls}&nbsp;</td>";
    $total_24h_line   .= '<td align="right" class="focusout">' . ($k==1?'&nbsp;(':'') . prettyNum($summary['trade_24h_sum'])   . '<span class="denom">'.trim($summary['denom'],'1') . "</span>" . ($pls==null?')':'') . "&nbsp;{$pls}&nbsp;</td>";
    $total_month_line .= '<td align="right" class="focusout">' . ($k==1?'&nbsp;(':'') . prettyNum($summary['trade_month_sum']) . '<span class="denom">'.trim($summary['denom'],'1') . "</span>" . ($pls==null?')':'') . "&nbsp;{$pls}&nbsp;</td>";
    $k++;
}

$total_usd       = prettyNum($total_usd);
$total_24h_usd   = prettyNum($total_24h_usd);
$total_month_usd = prettyNum($total_month_usd);

echo "
    <div class='row col-md-12'>
        <div class='col-md-8 summaries'>
            <table> 
                <tr class='summary_line'><td><span class='metric_name'>24Hours&nbsp;Total:&nbsp;</span> </td><td align='right'>{$total_24h_usd}&nbsp;USD&nbsp;   &nbsp;&nbsp;</td>  $total_24h_line</tr>
                <tr class='summary_line'><td><span class='metric_name'>Month&nbsp;Total:&nbsp;</span>   </td><td align='right'>{$total_month_usd}&nbsp;USD&nbsp; &nbsp;&nbsp;</td>$total_month_line</tr>
                <tr class='summary_line'><td><span class='metric_name'>Total:&nbsp;</span>         </td><td align='right'>{$total_usd}&nbsp;USD&nbsp;       &nbsp;&nbsp;</td>      $total_line</tr>
            </table>
        </div>
        <div class='col-md-4'><div class='timezone'>Timezone: $timezone</div></div>
    </div>
";

?>

<div class='col-md-12'><hr></div>

<?php

foreach ($markets as $market)
{
    $base = mb_strimwidth(trim($market->base,1),0,6,'..');
    $quote = mb_strimwidth(trim($market->quote,1),0,6,'..');
    $market_name = preg_replace('/\d/','',$market->name);

    echo "
            <div class='col-md-4'>
                <div class='panel'>
                    <div class='col-md-6 market_name'>{$market_name}</div>
                    <!--<div class='col-md-12 row'>
                        <div class='col-md-2'></div>
                        <div class='col-md-5 metric_name'>Count</div>
                        <div class='col-md-5 metric_name'>Sum</div>
                    </div>-->
                    <div class='col-md-12'>
                        <table>
                                <colgroup><col width='30%'><col width='15%'><col width='45%'><col width='10%'></colgroup>
                                <tr>
                                    <td></td>
                                    <td class='metric_name title_count' align='left'>Count</td>
                                    <td class='metric_name title_sum' align='right'>Sum</td>
                                    <td></td>
                                </tr>
                        </table>        
                    </div>
                    
                    <div class='col-md-12 row'>
                        <table>
                            <colgroup><col width='30%'><col width='15%'><col width='45%'><col width='10%'></colgroup>
                            <tr>
                                <td rowspan='2' class='col-md-3 period_name'>24Hours</td>
                                <td rowspan='2'>".prettyNum($market->h24CountA,0)."</td>
                                <td align='right'>".prettyNum($market->h24SumA)."</td>
                                <td align='left' title='{$market->base}' class='denom'>{$base}</td>
                            </tr>
                            <tr>
                                <!--<td></td>-->
                                <!--<td></td>-->
                                <td align='right'>".prettyNum($market->h24SumB)."</td>
                                <td align='left' title='{$market->quote}' class='denom'>{$quote}</td>
                            </tr>
                         </table>   
                    </div>
                    <div class='col-md-12'><hr></div>
                    <div class='col-md-12 row'>
                        <table>
                            <colgroup><col width='30%'><col width='15%'><col width='45%'><col width='10%'></colgroup>
                            <tr>
                                <td rowspan='2' class='col-md-3 period_name'>Month</td>
                                <td rowspan='2'>".prettyNum($market->monthCountA,0)."</td>
                                <td align='right'>".prettyNum($market->monthSumA)."</td>
                                <td align='left' title='{$market->base}' class='denom'>{$base}</td>
                            </tr>
                            <tr>
                                <!--<td></td>-->
                                <!--<td></td>-->
                                <td align='right'>".prettyNum($market->monthSumB)."</td>
                                <td align='left' title='{$market->quote}' class='denom'>{$quote}</td>
                            </tr>
                         </table>   
                    </div>
                    <div class='col-md-12'><hr></div>
                    <div class='col-md-12 row'>
                        <table>
                            <colgroup><col width='30%'><col width='15%'><col width='45%'><col width='10%'></colgroup>
                            <tr>
                                <td rowspan='2' class='col-md-3 period_name'>All time</td>
                                <td rowspan='2'>".prettyNum($market->countA,0)."</td>
                                <td align='right'>".prettyNum($market->sumA)."</td>
                                <td align='left' title='{$market->base}' class='denom'>{$base}</td>
                            </tr>
                            <tr>
                                <!--<td></td>-->
                                <!--<td></td>-->
                                <td align='right'>".prettyNum($market->sumB)."</td>
                                <td align='left' title='{$market->quote}' class='denom'>{$quote}</td>
                            </tr>
                         </table>   
                    </div>
                </div>
            </div>
    ";
}


function prettyNum($v,$d=2){
    return Yii::$app->formatter->format($v, ['decimal', $d]);
}

?>


