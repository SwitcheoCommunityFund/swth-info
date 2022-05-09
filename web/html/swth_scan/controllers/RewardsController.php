<?php

namespace app\controllers;

use Yii;
use app\models\Rewards;
use app\models\RewardsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;


/**
 * RewardsController implements the CRUD actions for Rewards model.
 */
class RewardsController extends BaseController
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
     * Lists all Rewards models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RewardsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rewards model.
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


    public function actionChart($wallet)
    {
        $byMonthExpr=new Expression("date_trunc('month',date)");


        return $this->render('chart', [
            'by_day' => Rewards::find()->select(['wallet','sum(value) as value','date(date) as date'])->where(['wallet'=>$wallet])->andWhere(['>','value','0'])->groupBy(['wallet','date(date)'])->orderBy('date(date)')->asArray()->all(),
            'by_month' => Rewards::find()->select(['wallet','sum(value) as value','date'=>$byMonthExpr])->where(['wallet'=>$wallet])->andWhere(['>','value','0'])->groupBy(['wallet',$byMonthExpr])->orderBy($byMonthExpr)->asArray()->all(),
        ]);
    }


    public function actionTest()
    {
        $searchModel = new RewardsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('test', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCharts()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $wallet = Yii::$app->request->post('wallet');

        $byDateExpr=new Expression("date(timezone('{$timezone}',date))");
        $byDay = Rewards::find()->alias('r')->joinWith('token t');
        $dayGroupBy = [$byDateExpr,'r.denom','t.decimals'];
        $daySelect  = ['sum(value) as value','date'=>$byDateExpr,'r.denom','t.decimals'];
        if($wallet) {
            $byDay->where(['wallet'=>$wallet]);
            $daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)->{$nextWhere}(['>','value','0'])->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();


        $byMonthExpr=new Expression("date_trunc('month',timezone('{$timezone}',date))");
        $byMonth = Rewards::find()->alias('r')->joinWith('token t');
        $monthGroupBy=[$byMonthExpr,'r.denom','t.decimals'];
        $monthSelect=['sum(value) as value','date'=>$byMonthExpr,'r.denom','t.decimals'];
        if($wallet) {
            $byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr)->asArray()->all();

        return $this->renderAjax('chart', [
            'by_day' => $byDay,
            'by_month' => $byMonth,
        ]);
    }

    public function actionChartByDay()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $wallet = Yii::$app->request->post('wallet');

        $lastYearExpr = new Expression("timezone('{$this->timezone}',current_date) - interval '1 year'");


        $byDateExpr=new Expression("date(timezone('{$this->timezone}',date))");
        $byDay = Rewards::find()->alias('r')->joinWith('token t');
        $dayGroupBy = [$byDateExpr,'r.denom'];
        $daySelect  = ['sum(value/pow(10,t.decimals)) as value','date'=>$byDateExpr,'r.denom'];
        if($wallet) {
            $byDay->where(['wallet'=>$wallet]);
            $daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)
                       ->{$nextWhere}(['>','value','0'])
                       ->andWhere(['>=',$byDateExpr,$lastYearExpr])
                       ->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();

        $data=[];
        foreach ($byDay as $item){
            if(!@$data[$item['denom']])
            {
                $data[$item['denom']]=[];
            }
            $data[$item['denom']][]=[
                substr($item['date'],0,10),
                round($item['value'],2)
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

        $lastYearExpr = new Expression("date_trunc('month',timezone('{$this->timezone}',current_date))- interval '12 months'");


        $byMonthExprChr=new Expression("to_char(date_trunc('month',timezone('{$this->timezone}',date)),'YYYY-MM-DD')");
        $byMonthExpr=new Expression("date_trunc('month',timezone('{$this->timezone}',date))");
        $byMonth = Rewards::find()->alias('r')->joinWith('token t');
        $monthGroupBy=[$byMonthExprChr,'r.denom'];
        $monthSelect=['sum(value/pow(10,t.decimals)) as value','date'=>$byMonthExprChr,'r.denom'];
        if($wallet) {
            $byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])
                           ->andWhere(['>=',$byMonthExpr,$lastYearExpr])
                           ->groupBy($monthGroupBy)->orderBy($byMonthExprChr)
                           ->asArray()->all();

        $data=[];
        foreach ($byMonth as $item){
            if(!@$data[$item['denom']])
            {
                $data[$item['denom']]=[];
            }
            $data[$item['denom']][]=[
                substr($item['date'],0,10),
                round($item['value'],2)
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


    public function actionExcelExport()
    {
        $query_params = Yii::$app->request->queryParams;

        if(!isset($query_params['RewardsSearch']['wallet']) || mb_strlen($query_params['RewardsSearch']['wallet'])<30){
            throw new \yii\web\ForbiddenHttpException('Wallet id is required for export data');
        }

        $searchModel = new RewardsSearch();
        $activeQuery = $searchModel->search($query_params, true);
        //var_dump($activeQuery->one()->toArray()); return;

        $fields_only = ['wallet','validator','value','denom','date'];

        $data = $activeQuery->select([
            'r.wallet',
            'validator',
            'value'=>new Expression('r.value / power(10,t.decimals)'),
            'r.denom',
            'date'
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
                'Rewards' => [
                    //'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'data' => $data,
                    'titles' => [
                        'Wallet',
                        'Validator',
                        'Value',
                        'Denom',
                        'Transaction time',
                    ],
                    'on beforeRender' => function ($event) {
                        $sheet = $event->sender->getSheet();
                        $sheet->getColumnDimension('A')->setWidth(50);
                        $sheet->getColumnDimension('B')->setWidth(50);
                        $sheet->getColumnDimension('C')->setWidth(20);
                        $sheet->getColumnDimension('D')->setWidth(10);
                        $sheet->getColumnDimension('E')->setWidth(30);
                    }
                ]
            ]
        ]);
        $file->send('rewards.xlsx');
    }

    public function actionStats()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $month = new Expression("sum(value) filter (where date_trunc('month',timezone('{$timezone}',date)) = date_trunc('month',timezone('{$timezone}',current_timestamp)))");
        $week = new Expression("sum(value) filter (where date_trunc('week',timezone('{$timezone}',date)) = date_trunc('week',timezone('{$timezone}',current_timestamp)))");

        return [
            'rewards' => @Rewards::find()->select(['month'=>$month, 'week'=>$week, 'denom'])
                ->where('denom is not null')
                ->groupBy(["denom"])
                ->having(['IS NOT', $month, null])->orHaving(['IS NOT', $week, null])
                ->orderBy('denom')
                ->asArray()->all(),
            'timezone'=>$timezone,
        ];
    }



    /**
     * Finds the Rewards model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rewards the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rewards::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
