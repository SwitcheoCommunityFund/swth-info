<?php

use Yii;
use yii\web\View;

$this->registerJsFile('/js/momentjs/moment.js',['position'=>yii\web\View::POS_HEAD]);
$this->registerJsFile('/js/apexcharts/apexcharts.min.js',['position'=>yii\web\View::POS_HEAD]);



$charts_controller = @$charts_controller?$charts_controller:Yii::$app->controller->id;
$hide_series = json_encode(@$hide_series?$hide_series:[]);
$show_series = json_encode(@$show_series?$show_series:[]);
$params = json_encode(@$params?$params:((object)[]));



$js = <<< JS

    /* ----------- view vars ----------- */
    
    var controller = '{$charts_controller}';
    var hide_series = {$hide_series};
    var show_seires = {$show_series};
    var params = {$params};

    /* ----------- conf vars ----------- */
    
    var charts_theme = Cookies.get('theme');
    var selection_backgr = '#a2b4cb';
    var selection_selector = '#90a3b2';
    var grid_border = '#8f8f8f';
    
    /* ------------------ DAILY CHART ------------------*/
    
    var daily_options = {
      chart: {
        id: 'chart2',
        type: 'area',
        toolbar:{
            show: false,
            autoSelected: 'zoom'
        },
        height: 350,
        width: '100%',
        background:'transparent',
        zoom: {
            autoScaleYaxis: true
          }
      },
      theme: {
        mode: charts_theme, 
      },
      grid: {
          borderColor: grid_border
      },
      series: [],
      stroke: {
        curve: 'smooth',
        width: 2,
        lineCap: 'round',
      },
      yaxis: {
          forceNiceScale:true,
          labels: {
              formatter: function(value){
                  return new Intl.NumberFormat('en-US', {
                    notation: "compact",
                    compactDisplay: "short"
                  }).format(value);
              }
          }
      },
      tooltip: {
          y: {
              formatter:function(value){
                  return (new Intl.NumberFormat('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
              }
          }
      },
      xaxis: {
          type: 'datetime',
          tickPlacement: 'on'
      },
      dataLabels:{
          enabled: false
      },
      legend: {
          show: false      
      }
    };
    
    var optionsLine = {
          series: [],
          chart: {
              id: 'chart1',
              height: 120,
              width: '100%',
              type: 'area',
              brush:{
                  target: 'chart2',
                  enabled: true
              },
              selection: {
                  enabled: true,
                  xaxis: {
                     min: +moment().add(-1,'M'),
                     max: +moment()
                  },
                  fill: {
                     color: selection_backgr,
                     opacity: 0.1
                  },
                  stroke: {
                     width: 1,
                     dashArray: 3,
                     color: selection_selector,
                     opacity: 0.4
                  },
              },
              background:'transparent',
              events: {
                  legendClick: function(chartContext, seriesIndex, config) {
                      console.log(chartContext,seriesIndex,config)  
                      var brush = config.config.chart.brush.target;
                      var series = config.globals.seriesNames;
                      var toggleSeries = series[seriesIndex];
                      ApexCharts.exec(brush,'toggleSeries',toggleSeries,true,true);
                  }
              }
          },
          grid: {
              borderColor: '#8f8f8f'
          },
          theme: {
              mode: charts_theme,
          },
          fill: {
              type: 'gradient',
              gradient: {
                  opacityFrom: 0.91,
                  opacityTo: 0.1,
              }
          },
          xaxis: {
              type: 'datetime',
              tooltip: {
                  enabled: false
              }
          },
          yaxis: {
              tickAmount: 2,
              labels: {
                  formatter: function(value){
                      return new Intl.NumberFormat('en-US', {
                        notation: "compact",
                        compactDisplay: "short"
                      }).format(value);
                  }
              },
          },
          legend: {
              show: true,
                   
          }
       };

    var dailyChart = new ApexCharts(document.querySelector("#daily-chart"), daily_options);
    dailyChart.render();

    var dailyChartLine = new ApexCharts(document.querySelector("#daily-chart-line"), optionsLine);
    dailyChartLine.render();
    
    loadDailyChart = function(data={})
    {
        $('.daily_chart_panel .chart_overlay').show();
        var xhr = $.post('/'+controller+'/chart-by-day',data,(res)=>
        {
            dailyChart.updateSeries(res.series);
            showOnlySeries(dailyChart, show_seires);
            hideSeries(dailyChart, hide_series);
            
            dailyChartLine.updateSeries(res.series);
            showOnlySeries(dailyChartLine, show_seires);
            hideSeries(dailyChartLine, hide_series);
            if(Object.keys(data).length==0){
                dailyChartLine.updateOptions({
                    chart: { selection: {enabled: true, xaxis: {min: +moment().add(-1,'M'),max: +moment()},}}
                })
            } /*else {
                dailyChartLine.updateOptions({
                    chart: {selection: {enabled: true, xaxis: {min: null,max: null},}}
                })
            }*/
        });
        xhr.done(()=>{
            $('.daily_chart_panel .chart_overlay').hide();
        });
        return xhr;
    }
    loadDailyChart(params);
    
    
    /* ------------------ MONTHLY CHART ------------------*/
    
    var monthly_options = {
      chart: {
        id: 'chart4',
        type: 'bar',
        height: 350,
        width: '100%',
        toolbar:{
            show: false,
            autoSelected: 'zoom'
        },
        background:'transparent'
      },
      theme: {
        mode: charts_theme, 
      },
      grid: {
          borderColor: grid_border
      },
      series: [],
      stroke: {
        curve: 'smooth',
        width: 2,
        lineCap: 'round',
      },
      yaxis: {
          labels: {
              formatter: function(value){
                  return new Intl.NumberFormat('en-US', {
                    notation: "compact",
                    compactDisplay: "short"
                  }).format(value);
              }
          }
      },
      tooltip: {
          y: {
              formatter:function(value){
                  return (new Intl.NumberFormat('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})).format(value);
              }
          }
      },
      xaxis: {
         type: 'datetime'
      },
      dataLabels:{
         enabled: false
      },
      legend: {
          show: false      
      }
    }
    
    var optionsLine = {
          series: [],
          chart: {
              id: 'chart3',
              height: 120,
              width: '100%',
              type: 'area',
              brush:{
                  target: 'chart4',
                  enabled: true
              },
              selection: {
                  enabled: true,
                  xaxis: {
                     min: +moment().add(-6,'M'),
                     max: +moment().startOf('month').utcOffset(0, true).unix()*1000
                  },
                  fill: {
                     color: selection_backgr,
                     opacity: 0.1
                  },
                  stroke: {
                     width: 1,
                     dashArray: 3,
                     color: selection_selector,
                     opacity: 0.4
                  },
              },
              background:'transparent',
              events: {
                  legendClick: function(chartContext, seriesIndex, config) {
                      console.log(chartContext,seriesIndex,config)  
                      var brush = config.config.chart.brush.target;
                      var series = config.globals.seriesNames;
                      var toggleSeries = series[seriesIndex];
                      ApexCharts.exec(brush,'toggleSeries',toggleSeries,true,true);
                  }
              }
          },
          grid: {
              borderColor: '#8f8f8f'
          },
          theme: {
              mode: charts_theme,
          },
          fill: {
              type: 'gradient',
              gradient: {
                  opacityFrom: 0.91,
                  opacityTo: 0.1,
              }
          },
          xaxis: {
              type: 'datetime',
              tooltip: {
                  enabled: false
              }
          },
          yaxis: {
              tickAmount: 2,
              labels: {
                  formatter: function(value){
                      return new Intl.NumberFormat('en-US', {
                        notation: "compact",
                        compactDisplay: "short"
                      }).format(value);
                  }
              },
          }
       };
    
    var monthlyChart = new ApexCharts(document.querySelector("#monthly-chart"), monthly_options);
    monthlyChart.render();
    
    var monthlyChartLine = new ApexCharts(document.querySelector("#monthly-chart-line"), optionsLine);
    monthlyChartLine.render();
    
    loadMonthlyChart = function(data={})
    {
        $('.monthly_chart_panel .chart_overlay').show();
        var xhr = $.post('/'+controller+'/chart-by-month',data,(res)=>
        {
            monthlyChart.updateSeries(res.series);
            showOnlySeries(monthlyChart, show_seires);
            hideSeries(monthlyChart, hide_series);
            
            monthlyChartLine.updateSeries(res.series);
            showOnlySeries(monthlyChartLine, show_seires);
            hideSeries(monthlyChartLine, hide_series);
            if(Object.keys(data).length==0){
                monthlyChartLine.updateOptions({
                    chart: { selection: {enabled: true, xaxis: {min: +moment().add(-6,'M'),max: +moment().startOf('month').utcOffset(0, true).unix()*1000},}}
                })
            } /*else {
                monthlyChartLine.updateOptions({
                    chart: {selection: {enabled: true, xaxis: {min: null,max: null},}}
                })
            }*/
        });
        xhr.done(()=>{
            $('.monthly_chart_panel .chart_overlay').hide();
        });
        return xhr;
    }
    loadMonthlyChart(params);
    
    function showOnlySeries(chart,series)
    {
        if(series.length>0)
        {
            var chart_series = chart.w.globals.seriesNames;
            for(var i in chart_series){
                if(!series.includes(chart_series[i]))
                chart.hideSeries(chart_series[i]);
            }
        }
    }
    
    function hideSeries(chart,series)
    {
        if(series.length>0)
        for(var i in series){
            chart.hideSeries(series[i]);
        }
    }

JS;
$this->registerJs($js,View::POS_READY);

?>
<style>
    .chart_overlay{
        background: rgba(255, 255, 255, 0.14);
        width: 100%;
        position: absolute;
        height: 460px;
        border-radius: 3px;
        z-index: 1000;
        left: 0px;
        display: none;
    }
    .chart_overlay .loader{
        margin-top: 180px;
        padding-left: 30px;
    }
    .daily_chart_panel,.monthly_chart_panel{
        padding: 0px 5px 0px 0px;
    }
    .apexcharts-legend {
        margin-bottom: -5px;
    }

</style>
<script>
    var loadDailyChart,loadMonthlyChart;
</script>
<div class="col-md-12 row">
    <div class="col-md-6">
        <div class="panel daily_chart_panel">
            <div class="chart_overlay">
                <div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>
            </div>
            <div id="daily-chart">

            </div>
            <div id="daily-chart-line" style="margin-top:-40px;">

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel monthly_chart_panel">
            <div class="chart_overlay">
                <div class=loader><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>
            </div>
            <div id="monthly-chart">

            </div>
            <div id="monthly-chart-line" style="margin-top:-40px;">

            </div>
        </div>
    </div>
</div>