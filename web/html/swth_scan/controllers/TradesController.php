<?php

namespace app\controllers;

use Yii;
use app\models\Trades;
use app\models\Markets;
use app\models\TradesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * TradesController implements the CRUD actions for Trades model.
 */
class TradesController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Trades models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TradesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Trades model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionMarket()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $groupingCount = new Expression('COUNT(*) cnta, COUNT(*) cntb');
        $groupingSum   = new Expression('SUM(quantity) suma, SUM(price*quantity) sumb'); //, SUM(price*quantity*th.current_price) sumb_usd


        $subTrades24 = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("block_created_at >= current_timestamp - interval '24 hours'")
            ->joinWith('m m')
            ->groupBy(['market']);

        /*$subTradesWeek = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("date_trunc('week',timezone('{$timezone}',block_created_at)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")
            ->joinWith('m m')
            ->groupBy(["date_trunc('week',timezone('{$timezone}',block_created_at))",'market']);*/

        $subTradesMonth = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")
            ->joinWith('m m')
            ->groupBy(["date_trunc('month',timezone('{$timezone}',block_created_at))",'market']);

        $subTradesAll = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->joinWith('m m')
            ->groupBy(['market']);



        $query = Markets::find()
            ->alias('mar')
            ->select(['mar.*',
                'monthCountA'=>'monstat.cnta',
                'monthCountB'=>'monstat.cntb',
                'monthSumA'  =>'round(monstat.suma::numeric,6)',
                'monthSumB'  =>'round(monstat.sumb::numeric,6)',

                'h24CountA'=>'h24stat.cnta',
                'h24CountB'=>'h24stat.cntb',
                'h24SumA'  =>'round(h24stat.suma::numeric,6)',
                'h24SumB'  =>'round(h24stat.sumb::numeric,6)',

                /*
                'weekCountA'=>'weekstat.cnta',
                'weekCountB'=>'weekstat.cntb',
                'weekSumA'  =>'round(weekstat.suma::numeric,6)',
                'weekSumB'  =>'round(weekstat.sumb::numeric,6)',
                */

                'countA'=>'stat.cnta',
                'countB'=>'stat.cntb',
                'sumA'  =>'round(stat.suma::numeric,6)',
                'sumB'  =>'round(stat.sumb::numeric,6)',
            ])
            ->leftJoin(['monstat' => $subTradesMonth],  'mar.name = monstat.market')
            //->leftJoin(['weekstat' => $subTradesWeek], 'mar.name = weekstat.market')
            ->leftJoin(['h24stat' => $subTrades24], 'mar.name = h24stat.market')
            ->leftJoin(['stat' => $subTradesAll],     'mar.name = stat.market');

        $monthTotalExpr = new Expression("SUM(price*quantity) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $monthTotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $H24TotalExpr = new Expression("SUM(price*quantity) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $H24TotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $rowCount = new Expression("count(*) over (order by 1)");

        $tradesSummary = Trades::find()
            ->select([
                'denom'=>'m.quote',
                'trade_sum'=>'SUM(price*quantity)',
                'trade_sum_usd'=>'SUM(price*quantity*COALESCE(th.current_price,tn.current_price))',
                'trade_month_sum'=>$monthTotalExpr,
                'trade_month_sum_usd'=>$monthTotalExprUsd,
                'trade_24h_sum'=>$H24TotalExpr,
                'trade_24h_sum_usd'=>$H24TotalExprUsd,
                'count'=>$rowCount
            ])
            ->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date")

            ->groupBy(["m.quote"])
            ->orderBy('SUM(price*quantity) desc')
        ;

        return $this->render('market', [
            //'query'        => $query->createCommand()->getRawSql(),
            'markets'        => $query->all(),
            'trades_summary' => $tradesSummary->asArray()->all(),
            'timezone'       => $timezone
        ]);
    }

    public function actionMarketTest()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $groupingCount = new Expression('COUNT(*) cnta, COUNT(*) cntb');
        $groupingSum   = new Expression('SUM(quantity) suma, SUM(price*quantity) sumb'); //, SUM(price*quantity*th.current_price) sumb_usd


        $subTrades24 = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("block_created_at >= current_timestamp - interval '24 hours'")
            ->joinWith('m m')
            ->groupBy(['market']);

        /*$subTradesWeek = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("date_trunc('week',timezone('{$timezone}',block_created_at)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")
            ->joinWith('m m')
            ->groupBy(["date_trunc('week',timezone('{$timezone}',block_created_at))",'market']);*/

        $subTradesMonth = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")
            ->joinWith('m m')
            ->groupBy(["date_trunc('month',timezone('{$timezone}',block_created_at))",'market']);

        $subTradesAll = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->joinWith('m m')
            ->groupBy(['market']);



        $query = Markets::find()
            ->alias('mar')
            ->select(['mar.*',
                'monthCountA'=>'monstat.cnta',
                'monthCountB'=>'monstat.cntb',
                'monthSumA'  =>'round(monstat.suma::numeric,6)',
                'monthSumB'  =>'round(monstat.sumb::numeric,6)',

                'h24CountA'=>'h24stat.cnta',
                'h24CountB'=>'h24stat.cntb',
                'h24SumA'  =>'round(h24stat.suma::numeric,6)',
                'h24SumB'  =>'round(h24stat.sumb::numeric,6)',

                /*
                'weekCountA'=>'weekstat.cnta',
                'weekCountB'=>'weekstat.cntb',
                'weekSumA'  =>'round(weekstat.suma::numeric,6)',
                'weekSumB'  =>'round(weekstat.sumb::numeric,6)',
                */

                'countA'=>'stat.cnta',
                'countB'=>'stat.cntb',
                'sumA'  =>'round(stat.suma::numeric,6)',
                'sumB'  =>'round(stat.sumb::numeric,6)',
            ])
            ->leftJoin(['monstat' => $subTradesMonth],  'mar.name = monstat.market')
            //->leftJoin(['weekstat' => $subTradesWeek], 'mar.name = weekstat.market')
            ->leftJoin(['h24stat' => $subTrades24], 'mar.name = h24stat.market')
            ->leftJoin(['stat' => $subTradesAll],     'mar.name = stat.market');

        $monthTotalExpr = new Expression("SUM(price*quantity) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $monthTotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $H24TotalExpr = new Expression("SUM(price*quantity) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $H24TotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $rowCount = new Expression("count(*) over (order by 1)");

        $tradesSummary = Trades::find()
            ->select([
                'denom'=>'m.quote',
                'trade_sum'=>'SUM(price*quantity)',
                'trade_sum_usd'=>'SUM(price*quantity*COALESCE(th.current_price,tn.current_price))',
                'trade_month_sum'=>$monthTotalExpr,
                'trade_month_sum_usd'=>$monthTotalExprUsd,
                'trade_24h_sum'=>$H24TotalExpr,
                'trade_24h_sum_usd'=>$H24TotalExprUsd,
                'count'=>$rowCount
            ])
            ->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date")

            ->groupBy(["m.quote"])
            ->orderBy('SUM(price*quantity) desc')
        ;

        return $this->render('market-test', [
            //'query'        => $query->createCommand()->getRawSql(),
            'markets'        => $query->all(),
            'trades_summary' => $tradesSummary->asArray()->all(),
            'timezone'       => $timezone
        ]);
    }

    public function actionChartByDay()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $wallet = Yii::$app->request->post('wallet');

        $lastYearExpr = new Expression("timezone('{$this->timezone}',current_date) - interval '1 year'");
        $byDateExpr=new Expression("date(timezone('{$this->timezone}',block_created_at))");
        $byDay = Trades::find()->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date");
        $dayGroupBy = [$byDateExpr,'m.quote','m.quote_precision'];
        $daySelect  = [
            'value'=>'SUM(price*quantity)',
            'date'=>$byDateExpr,
            'denom'=>'m.quote',
            'decimals'=>'m.quote_precision',
            'price'=>'avg(th.current_price)',
            'usd'=>'sum(price*quantity*COALESCE(th.current_price,tn.current_price))'
        ];
        if($wallet) {
            $byDay->where(['maker_address'=>$wallet]);
            $daySelect[]='maker_address';
            $dayGroupBy[]='maker_address';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)/*->{$nextWhere}(['>','value','0'])*/
            ->andWhere(['>',"timezone('{$this->timezone}',block_created_at)",$lastYearExpr])
            ->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();

        $data=[];

        $data['Total USD']=[];
        $usds = [];

        foreach ($byDay as $item)
        {
            if(!@$data[$item['denom']])
            {
                $data[$item['denom']]=[];
            }
            $data[$item['denom']][]=[
                substr($item['date'],0,10),
                round($item['value'],2)
            ];
            if(!@$usds[substr($item['date'],0,10)]){
                $usds[substr($item['date'],0,10)] = $item['usd'];
            } else $usds[substr($item['date'],0,10)] += $item['usd'];
        }
        foreach ($usds as $date => $usd){
            $data['Total USD'][]=[
                $date,
                round($usd,2)
            ];
        }


        $series=[];
        foreach ($data as $denom=>$data_pack){
            $series[]=[
                'name' => $denom,
                'data' => $data_pack,
            ];
        }

        return [
            'series'=>$series
        ];
    }

    public function actionChartByMonth()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $wallet = Yii::$app->request->post('wallet');

        $lastYearExpr = new Expression("timezone('{$this->timezone}',current_date) - interval '1 year'");
        $byMonthExpr=new Expression("date_trunc('month',timezone('{$this->timezone}',block_created_at))");
        $byMonth = Trades::find()->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date");
        $monthGroupBy=[$byMonthExpr,'m.quote','m.quote_precision'];
        $monthSelect=[
            'value'=>'SUM(price*quantity)',
            'date'=>$byMonthExpr,
            'denom'=>'m.quote',
            'decimals'=>'m.quote_precision',
            'price'=>'avg(th.current_price)',
            'usd'=>'sum(price*quantity*COALESCE(th.current_price,tn.current_price))'
        ];
        if($wallet) {
            $byMonth->where(['maker_address'=>$wallet]);
            $monthGroupBy[]='maker_address';
            $monthSelect[]='maker_address';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)/*->{$nextWhere}(['>','value','0'])*/
            ->andWhere(['>',"timezone('{$this->timezone}',block_created_at)",$lastYearExpr])
            ->groupBy($monthGroupBy)->orderBy($byMonthExpr)->asArray()->all();

        $data=[];

        $data['Total USD']=[];
        $usds = [];

        foreach ($byMonth as $item)
        {
            if(!@$data[$item['denom']])
            {
                $data[$item['denom']]=[];
            }
            $data[$item['denom']][]=[
                substr($item['date'],0,10),
                round($item['value'],2)
            ];
            if(!@$usds[substr($item['date'],0,10)]){
                $usds[substr($item['date'],0,10)] = $item['usd'];
            } else $usds[substr($item['date'],0,10)] += $item['usd'];
        }
        foreach ($usds as $date => $usd){
            $data['Total USD'][]=[
                $date,
                round($usd,2)
            ];
        }


        $series=[];
        foreach ($data as $denom=>$data_pack){
            $series[]=[
                'name' => $denom,
                'data' => $data_pack,
            ];
        }

        return [
            'series'=>$series
        ];
    }

    public function holdactionMarketGetQuery()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $groupingCount = new Expression('COUNT(*) cnta, COUNT(*) cntb');
        $groupingSum   = new Expression('SUM(quantity) suma, SUM(price*quantity) sumb'); //, SUM(price*quantity*th.current_price) sumb_usd


        $subTrades24 = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("block_created_at >= current_timestamp - interval '24 hours'")
            ->joinWith('m m')
            ->groupBy(['market']);

        /*$subTradesWeek = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("date_trunc('week',timezone('{$timezone}',block_created_at)) = date_trunc('week',timezone('{$timezone}',current_timestamp))")
            ->joinWith('m m')
            ->groupBy(["date_trunc('week',timezone('{$timezone}',block_created_at))",'market']);*/

        $subTradesMonth = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->where("date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp))")
            ->joinWith('m m')
            ->groupBy(["date_trunc('month',timezone('{$timezone}',block_created_at))",'market']);

        $subTradesAll = Trades::find()
            ->select(['market', $groupingCount, $groupingSum])
            ->joinWith('m m')
            ->groupBy(['market']);



        $query = Markets::find()
            ->alias('mar')
            ->select(['mar.*',
                'monthCountA'=>'monstat.cnta',
                'monthCountB'=>'monstat.cntb',
                'monthSumA'  =>'round(monstat.suma::numeric,6)',
                'monthSumB'  =>'round(monstat.sumb::numeric,6)',

                'h24CountA'=>'h24stat.cnta',
                'h24CountB'=>'h24stat.cntb',
                'h24SumA'  =>'round(h24stat.suma::numeric,6)',
                'h24SumB'  =>'round(h24stat.sumb::numeric,6)',

                /*
                'weekCountA'=>'weekstat.cnta',
                'weekCountB'=>'weekstat.cntb',
                'weekSumA'  =>'round(weekstat.suma::numeric,6)',
                'weekSumB'  =>'round(weekstat.sumb::numeric,6)',
                */

                'countA'=>'stat.cnta',
                'countB'=>'stat.cntb',
                'sumA'  =>'round(stat.suma::numeric,6)',
                'sumB'  =>'round(stat.sumb::numeric,6)',
            ])
            ->leftJoin(['monstat' => $subTradesMonth],  'mar.name = monstat.market')
            //->leftJoin(['weekstat' => $subTradesWeek], 'mar.name = weekstat.market')
            ->leftJoin(['h24stat' => $subTrades24], 'mar.name = h24stat.market')
            ->leftJoin(['stat' => $subTradesAll],     'mar.name = stat.market');

        $monthTotalExpr = new Expression("SUM(price*quantity) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $monthTotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where date_trunc('month',timezone('{$timezone}',block_created_at)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $H24TotalExpr = new Expression("SUM(price*quantity) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $H24TotalExprUsd = new Expression("SUM(price*quantity*COALESCE(th.current_price,tn.current_price)) filter (where block_created_at >= current_timestamp - interval '24 hours')");
        $rowCount = new Expression("count(*) over (order by 1)");

        $tradesSummary = Trades::find()
            ->select([
                'denom'=>'m.quote',
                'trade_sum'=>'SUM(price*quantity)',
                'trade_sum_usd'=>'SUM(price*quantity*COALESCE(th.current_price,tn.current_price))',
                'trade_month_sum'=>$monthTotalExpr,
                'trade_month_sum_usd'=>$monthTotalExprUsd,
                'trade_24h_sum'=>$H24TotalExpr,
                'trade_24h_sum_usd'=>$H24TotalExprUsd,
                'count'=>$rowCount
            ])
            ->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date")

            ->groupBy(["m.quote"])
            ->orderBy('SUM(price*quantity) desc')
        ;

        return [
            'trades_summary'=>$tradesSummary->createCommand()->getRawSql(),
            'market'=>$query->createCommand()->getRawSql()
        ];
    }

    public function actionCharts()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $wallet = Yii::$app->request->post('wallet');

        $byDateExpr=new Expression("date(timezone('{$timezone}',block_created_at))");
        $byDay = Trades::find()->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date");
        $dayGroupBy = [$byDateExpr,'m.quote','m.quote_precision'];
        $daySelect  = [
            'value'=>'SUM(price*quantity)',
            'date'=>$byDateExpr,
            'denom'=>'m.quote',
            'decimals'=>'m.quote_precision',
            'price'=>'avg(th.current_price)',
            'usd'=>'sum(price*quantity*COALESCE(th.current_price,tn.current_price))'
        ];
        if($wallet) {
            $byDay->where(['maker_address'=>$wallet]);
            $daySelect[]='maker_address';
            $dayGroupBy[]='maker_address';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)/*->{$nextWhere}(['>','value','0'])*/->groupBy($dayGroupBy)->orderBy($byDateExpr);


        $byMonthExpr=new Expression("date_trunc('month',timezone('{$timezone}',block_created_at))");
        $byMonth = Trades::find()->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date");
        $monthGroupBy=[$byMonthExpr,'m.quote','m.quote_precision'];
        $monthSelect=[
            'value'=>'SUM(price*quantity)',
            'date'=>$byMonthExpr,
            'denom'=>'m.quote',
            'decimals'=>'m.quote_precision',
            'price'=>'avg(th.current_price)',
            'usd'=>'sum(price*quantity*COALESCE(th.current_price,tn.current_price))'
        ];
        if($wallet) {
            $byMonth->where(['maker_address'=>$wallet]);
            $monthGroupBy[]='maker_address';
            $monthSelect[]='maker_address';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)/*->{$nextWhere}(['>','value','0'])*/->groupBy($monthGroupBy)->orderBy($byMonthExpr);

        $render = Yii::$app->request->isAjax?'renderAjax':'render';

        return $this->{$render}('chart', [
            'by_day' => $byDay->asArray()->all(),
            'by_month' => $byMonth->asArray()->all(),
            //'by_day_q' => $byDay->createCommand()->getRawSql(),
            //'by_month_q' => $byMonth->createCommand()->getRawSql(),
        ]);
    }

    public function actionChartsTest()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $wallet = Yii::$app->request->post('wallet');

        $byDateExpr=new Expression("date(timezone('{$timezone}',block_created_at))");
        $byDay = Trades::find()->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date");
        $dayGroupBy = [$byDateExpr,'m.quote','m.quote_precision'];
        $daySelect  = [
            'value'=>'SUM(price*quantity)',
            'date'=>$byDateExpr,
            'denom'=>'m.quote',
            'decimals'=>'m.quote_precision',
            'price'=>'avg(th.current_price)',
            'usd'=>'sum(price*quantity*COALESCE(th.current_price,tn.current_price))'
        ];
        if($wallet) {
            $byDay->where(['maker_address'=>$wallet]);
            $daySelect[]='maker_address';
            $dayGroupBy[]='maker_address';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)/*->{$nextWhere}(['>','value','0'])*/->groupBy($dayGroupBy)->orderBy($byDateExpr);


        $byMonthExpr=new Expression("date_trunc('month',timezone('{$timezone}',block_created_at))");
        $byMonth = Trades::find()->alias('tr')
            ->leftJoin(['m' => 'markets'],'m.name=tr.market')
            ->leftJoin(['t' => 'tokens'],'m.quote=cast(t.denom as text)')
            ->leftJoin(['th' => 'token_history'],"th.currency = 'usd' AND t.coin_gecko_id is not null AND th.id=t.coin_gecko_id and th.date = date(block_created_at)")
            ->leftJoin(['tn' => 'token_price_now'],"tn.currency = 'usd' AND t.coin_gecko_id is not null AND tn.id=t.coin_gecko_id and date(block_created_at)+1 >= current_date");
        $monthGroupBy=[$byMonthExpr,'m.quote','m.quote_precision'];
        $monthSelect=[
            'value'=>'SUM(price*quantity)',
            'date'=>$byMonthExpr,
            'denom'=>'m.quote',
            'decimals'=>'m.quote_precision',
            'price'=>'avg(th.current_price)',
            'usd'=>'sum(price*quantity*COALESCE(th.current_price,tn.current_price))'
        ];
        if($wallet) {
            $byMonth->where(['maker_address'=>$wallet]);
            $monthGroupBy[]='maker_address';
            $monthSelect[]='maker_address';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)/*->{$nextWhere}(['>','value','0'])*/->groupBy($monthGroupBy)->orderBy($byMonthExpr);

        $render = Yii::$app->request->isAjax?'renderAjax':'render';

        return $this->{$render}('chart-test', [
            'by_day' => $byDay->asArray()->all(),
            'by_month' => $byMonth->asArray()->all(),
            'by_day_q' => $byDay->createCommand()->getRawSql(),
            //'by_month_q' => $byMonth->createCommand()->getRawSql(),
        ]);
    }

    public function actionExcelExportTest()
    {
        $query_params = Yii::$app->request->queryParams;

        if(!isset($query_params['TradesSearch']['wallet']) || mb_strlen($query_params['TradesSearch']['wallet'])<30){
            throw new \yii\web\ForbiddenHttpException('Wallet id is required for export data');
        }

        $searchModel = new TradesSearch();
        $activeQuery = $searchModel->search($query_params, true);
        $activeQueryClone = clone $activeQuery;
        $count = $activeQuery->count();
        if($count>=1000){
            throw new \yii\web\HttpException(400, 'Too many lines in selection, please use the date filter to limit it and try again.');
        }


        //var_dump($activeQuery->one()->toArray()); return;

        $fields_only = ['block_created_at', 'taker_address', 'taker_fee_amount', 'taker_fee_denom', 'taker_side', 'maker_address', 'maker_fee_amount', 'maker_fee_denom', 'maker_side', 'market', 'price','price_denom', 'quantity', 'usd_price'];

        $data = $activeQuery->select([
            'block_created_at',
            //'taker_id',
            'taker_address',
            'taker_fee_amount',
            'taker_fee_denom',
            'taker_side',
            //'maker_id',
            'maker_address',
            'maker_fee_amount',
            'maker_fee_denom',
            'maker_side',
            'market',
            'price',
            'price_denom'=>'m.quote',
            'quantity',
            'usd_price'=>new Expression('tr.price * th.current_price'),
            //'liquidation',
            //'block_height',
        ])->asArray()->all();

        foreach ($data as $k=>$row)
        {
            foreach ($row as $field=>&$col) {
                if(!in_array($field,$fields_only)) unset($data[$k][$field]);
            }
        }

        //$activeQuery->andWhere(['wallet'=>'swth1dt5pspkyny95nav985pracu5g7c4lj6tv73dmx']);

        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'Trades' => [
                    //'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'data' => $data,
                    'titles' => [
                        'Date',
                        //'taker_id',
                        'Taker Wallet',
                        'Taker Fee',
                        'Taker Fee Denom',
                        'Taker Side',
                        //'maker_id',
                        'Maker Wallet',
                        'Maker Fee',
                        'Maker Fee Denom',
                        'Maker Side',
                        'Market',
                        'Price',
                        'Price Denom',
                        'Quantity',
                        'USD Price'
                    ],
                    'on beforeRender' => function ($event) {
                        $sheet = $event->sender->getSheet();
                        $sheet->getColumnDimension('A')->setWidth(28);
                        $sheet->getColumnDimension('B')->setWidth(45);
                        $sheet->getColumnDimension('C')->setWidth(18);
                        $sheet->getColumnDimension('D')->setWidth(14);
                        $sheet->getColumnDimension('E')->setWidth(9);
                        $sheet->getColumnDimension('F')->setWidth(45);
                        $sheet->getColumnDimension('G')->setWidth(18);
                        $sheet->getColumnDimension('H')->setWidth(14);
                        $sheet->getColumnDimension('I')->setWidth(9);
                        $sheet->getColumnDimension('J')->setWidth(12);
                        $sheet->getColumnDimension('K')->setWidth(18);
                        $sheet->getColumnDimension('L')->setWidth(13);
                        $sheet->getColumnDimension('M')->setWidth(10);
                        $sheet->getColumnDimension('N')->setWidth(18);
                    }
                ]
            ]
        ]);
        $file->send('trades.xlsx');
    }

    public function actionExcelExport()
    {
        $query_params = Yii::$app->request->queryParams;

        if(!isset($query_params['TradesSearch']['wallet']) || mb_strlen($query_params['TradesSearch']['wallet'])<30){
            throw new \yii\web\ForbiddenHttpException('Wallet id is required for export data');
        }

        $searchModel = new TradesSearch();
        $activeQuery = $searchModel->search($query_params, true);
        $activeQueryClone = clone $activeQuery;
        $count = $activeQueryClone->count();
        if($count>=7000){
            throw new \yii\web\HttpException(400, 'Too many lines in selection, please use the date filter to limit it and try again.');
        }
        //var_dump($activeQuery->one()->toArray()); return;

        $fields_only = ['block_created_at', 'taker_address', 'taker_fee_amount', 'taker_fee_denom', 'taker_side', 'maker_address', 'maker_fee_amount', 'maker_fee_denom', 'maker_side', 'market', 'price','price_denom', 'quantity', 'usd_price'];

        $data = $activeQuery->select([
            'block_created_at',
            //'taker_id',
            'taker_address',
            'taker_fee_amount',
            'taker_fee_denom',
            'taker_side',
            //'maker_id',
            'maker_address',
            'maker_fee_amount',
            'maker_fee_denom',
            'maker_side',
            'market',
            'price',
            'price_denom'=>'m.quote',
            'quantity',
            'usd_price'=>new Expression('tr.price * th.current_price'),
            //'liquidation',
            //'block_height',
        ])->asArray()->all();

        foreach ($data as $k=>$row)
        {
            foreach ($row as $field=>&$col) {
                if(!in_array($field,$fields_only)) unset($data[$k][$field]);
            }
        }

        //$activeQuery->andWhere(['wallet'=>'swth1dt5pspkyny95nav985pracu5g7c4lj6tv73dmx']);

        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'Trades' => [
                    //'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'data' => $data,
                    'titles' => [
                        'Date',
                        //'taker_id',
                        'Taker Wallet',
                        'Taker Fee',
                        'Taker Fee Denom',
                        'Taker Side',
                        //'maker_id',
                        'Maker Wallet',
                        'Maker Fee',
                        'Maker Fee Denom',
                        'Maker Side',
                        'Market',
                        'Price',
                        'Price Denom',
                        'Quantity',
                        'USD Price'
                    ],
                    'on beforeRender' => function ($event) {
                        $sheet = $event->sender->getSheet();
                        $sheet->getColumnDimension('A')->setWidth(28);
                        $sheet->getColumnDimension('B')->setWidth(45);
                        $sheet->getColumnDimension('C')->setWidth(18);
                        $sheet->getColumnDimension('D')->setWidth(14);
                        $sheet->getColumnDimension('E')->setWidth(9);
                        $sheet->getColumnDimension('F')->setWidth(45);
                        $sheet->getColumnDimension('G')->setWidth(18);
                        $sheet->getColumnDimension('H')->setWidth(14);
                        $sheet->getColumnDimension('I')->setWidth(9);
                        $sheet->getColumnDimension('J')->setWidth(12);
                        $sheet->getColumnDimension('K')->setWidth(18);
                        $sheet->getColumnDimension('L')->setWidth(13);
                        $sheet->getColumnDimension('M')->setWidth(10);
                        $sheet->getColumnDimension('N')->setWidth(18);
                    }
                ]
            ]
        ]);
        $file->send('trades.xlsx');
    }

    /**
     * Finds the Trades model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Trades the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Trades::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
