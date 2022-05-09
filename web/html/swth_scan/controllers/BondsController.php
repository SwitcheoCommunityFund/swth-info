<?php

namespace app\controllers;

use Yii;
use app\models\Bonds;
use app\models\BondsSearch;
use app\models\Delegates;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * BondsController implements the CRUD actions for Bonds model.
 */
class BondsController extends BaseController
{


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    /**
     * Lists all Bonds models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BondsSearch();
        $searchModel->useStakingLapse=true;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndexTest()
    {
        $searchModel = new BondsSearch();
        $searchModel->useStakingLapse=true;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_test', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionTest()
    {
        $searchModel = new BondsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('test', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChart($wallet)
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $wallet = Yii::$app->request->get('wallet');

        //$waitSepExpr = new Expression("case when date + interval '30 days' <= current_timestamp then false else true end");

        $byDateExpr = new Expression("date(timezone('{$timezone}',date))");
        $byDate30Expr = new Expression("date(timezone('{$timezone}',date) + interval '30 days')");
        $byDay = Bonds::find();
        $dayGroupBy = [$byDateExpr,$byDate30Expr];
        $daySelect  = ['sum(value) as value','date'=>$byDateExpr, 'date_unstakes'=>$byDate30Expr];
        if($wallet) {
            $byDay->where(['wallet'=>$wallet]);
            $daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)->{$nextWhere}(['>','value','0'])->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();


        $deleg_ByDay = Delegates::find();
        $dayGroupBy = [$byDateExpr];
        $daySelect  = ['sum(value) as value','date'=>$byDateExpr];
        if($wallet) {
            $deleg_ByDay->where(['wallet'=>$wallet]);
            $daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_ByDay = $deleg_ByDay->select($daySelect)->{$nextWhere}(['>','value','0'])->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();


        $byMonthExpr=new Expression("date_trunc('month',timezone('{$timezone}',date))");
        $byMonth = Bonds::find();
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value) as value','date'=>$byMonthExpr];
        if($wallet) {
            $byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr)->asArray()->all();


        $byMonth30Expr = new Expression("date_trunc('month',timezone('{$timezone}',date) + interval '30 days')");
        $byMonthUs = Bonds::find();
        $monthGroupBy=[$byMonth30Expr];
        $monthSelect=['sum(value) as value','date_unstakes'=>$byMonth30Expr];
        if($wallet) {
            $byMonthUs->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonthUs = $byMonthUs->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonth30Expr)->asArray()->all();


        $deleg_byMonth = Delegates::find();
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value) as value','date'=>$byMonthExpr];
        if($wallet) {
            $deleg_byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_byMonth = $deleg_byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr)->asArray()->all();

        return $this->render('chart', [
            'by_day' => $byDay,
            'delegate_by_day' => $deleg_ByDay,
            'by_month' => $byMonth,
            'by_month_us' => $byMonthUs,
            'delegate_by_month' => $deleg_byMonth,
            'wallet'=>$wallet
        ]);
    }

    public function actionCharts()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $wallet = Yii::$app->request->post('wallet');

        //$waitSepExpr = new Expression("case when date + interval '30 days' <= current_timestamp then false else true end");

        $byDateExpr = new Expression("date(timezone('{$timezone}',date))");
        $byDate30Expr = new Expression("date(timezone('{$timezone}',date) + interval '30 days')");
        $byDay = Bonds::find();
        $dayGroupBy = [$byDateExpr,$byDate30Expr];
        $daySelect  = ['sum(value) as value','date'=>$byDateExpr, 'date_unstakes'=>$byDate30Expr];
        if($wallet) {
            $byDay->where(['wallet'=>$wallet]);
            $daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)->{$nextWhere}(['>','value','0'])->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();


        $deleg_ByDay = Delegates::find();
        $dayGroupBy = [$byDateExpr];
        $daySelect  = ['sum(value) as value','date'=>$byDateExpr];
        if($wallet) {
            $deleg_ByDay->where(['wallet'=>$wallet]);
            $daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_ByDay = $deleg_ByDay->select($daySelect)->{$nextWhere}(['>','value','0'])->groupBy($dayGroupBy)->orderBy($byDateExpr)->asArray()->all();


        $byMonthExpr=new Expression("date_trunc('month',timezone('{$timezone}',date))");
        $byMonth = Bonds::find();
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value) as value','date'=>$byMonthExpr];
        if($wallet) {
            $byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr)->asArray()->all();


        $byMonth30Expr = new Expression("date_trunc('month',timezone('{$timezone}',date) + interval '30 days')");
        $byMonthUs = Bonds::find();
        $monthGroupBy=[$byMonth30Expr];
        $monthSelect=['sum(value) as value','date_unstakes'=>$byMonth30Expr];
        if($wallet) {
            $byMonthUs->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonthUs = $byMonthUs->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonth30Expr)->asArray()->all();


        $deleg_byMonth = Delegates::find();
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value) as value','date'=>$byMonthExpr];
        if($wallet) {
            $deleg_byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_byMonth = $deleg_byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr)->asArray()->all();

        return $this->renderAjax('chart', [
            'by_day' => $byDay,
            'delegate_by_day' => $deleg_ByDay,
            'by_month' => $byMonth,
            'by_month_us' => $byMonthUs,
            'delegate_by_month' => $deleg_byMonth,
            'wallet'=>$wallet
        ]);
    }


    public function actionChartByDay()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $wallet = Yii::$app->request->post('wallet');

        $lastYearExpr = new Expression("timezone('{$this->timezone}',current_date) - interval '1 year'");

        $byDateExpr = new Expression("date(timezone('{$this->timezone}',date))");
        $byDate30Expr = new Expression("date(timezone('{$this->timezone}',date) + interval '30 days')");
        $byDay = Bonds::find();
        $byDay->alias('b');
        $byDay->leftJoin(['t' => 'tokens'],'b.denom=t.denom');
        $dayGroupBy = [$byDateExpr,$byDate30Expr];
        $daySelect  = ['sum(value/pow(10,t.decimals)) as y','x'=>$byDateExpr];
        if($wallet) {
            $byDay->where(['wallet'=>$wallet]);
            //$daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byDay = $byDay->select($daySelect)->{$nextWhere}(['>','value','0'])->andWhere(['>=',$byDateExpr,$lastYearExpr])->groupBy($dayGroupBy)->orderBy($byDateExpr);

        $deleg_ByDay = Delegates::find();
        $deleg_ByDay->alias('b');
        $deleg_ByDay->leftJoin(['t' => 'tokens'],'b.denom=t.denom');
        $dayGroupBy = [$byDateExpr];
        $daySelect  = ['sum(value/pow(10,t.decimals)) as y','x'=>$byDateExpr];
        if($wallet) {
            $deleg_ByDay->where(['wallet'=>$wallet]);
            //$daySelect[]='wallet';
            $dayGroupBy[]='wallet';$nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_ByDay = $deleg_ByDay->select($daySelect)->{$nextWhere}(['>','value','0'])->andWhere(['>=',$byDateExpr,$lastYearExpr])->groupBy($dayGroupBy)->orderBy($byDateExpr);


        return [
            'series'=>[
                [
                    'name'=>'Unstaking started',
                    'data' => $byDay->asArray()->all()
                ],
                [
                    'name'=>'Unstaking ends',
                    'data' => $byDay->select(['sum(value/pow(10,t.decimals)) as y','x'=>$byDate30Expr])->asArray()->all()
                ],
                [
                    'name'=>'Staked',
                    'data' => $deleg_ByDay->asArray()->all()
                ]
            ]
        ];
    }

    public function actionChartByMonth()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $wallet = Yii::$app->request->post('wallet');

        $lastYearExpr = new Expression("date_trunc('month',timezone('{$this->timezone}',current_date))- interval '12 months'");

        $byMonthExpr=new Expression("to_char(date_trunc('month',timezone('{$this->timezone}',date)),'YYYY-MM-DD')");
        $byMonth = Bonds::find();
        $byMonth->alias('b');
        $byMonth->leftJoin(['t' => 'tokens'],'b.denom=t.denom');
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value/pow(10,t.decimals)) as y','x'=>$byMonthExpr];
        if($wallet) {
            $byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonth = $byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr);


        $byMonth30Expr = new Expression("date_trunc('month',timezone('{$this->timezone}',date) + interval '30 days')");
        $byMonthUs = Bonds::find();
        $byMonthUs->alias('b');
        $byMonthUs->leftJoin(['t' => 'tokens'],'b.denom=t.denom');
        $monthGroupBy=[$byMonth30Expr];
        $monthSelect=['sum(value/pow(10,t.decimals)) as y','x'=>$byMonth30Expr];
        if($wallet) {
            $byMonthUs->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $byMonthUs = $byMonthUs->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonth30Expr);


        $deleg_byMonth = Delegates::find();
        $deleg_byMonth->alias('b');
        $deleg_byMonth->leftJoin(['t' => 'tokens'],'b.denom=t.denom');
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value/pow(10,t.decimals)) as y','x'=>$byMonthExpr];
        if($wallet) {
            $deleg_byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_byMonth = $deleg_byMonth->select($monthSelect)->{$nextWhere}(['>','value','0'])->groupBy($monthGroupBy)->orderBy($byMonthExpr);


        return [
            'series'=>[
                [
                    'name'=>'Unstaking started',
                    'data' => $byMonth->asArray()->all()
                ],
                [
                    'name'=>'Unstaking ends',
                    'data' => $byMonthUs->asArray()->all()
                ],
                [
                    'name'=>'Staked',
                    'data' => $deleg_byMonth->asArray()->all()
                ]
            ]
        ];
    }

    public function actionTestNewChart()
    {
        return $this->render('//site/testeg',[]);
    }



    /**
     * Displays a single Bonds model.
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

    /**
     * Finds the Bonds model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bonds the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bonds::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
