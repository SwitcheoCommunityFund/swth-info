<?php

namespace app\controllers;

use Yii;
use app\models\Delegates;
use app\models\DelegatesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * DelegatesController implements the CRUD actions for Delegates model.
 */
class DelegatesController extends BaseController
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
     * Lists all Delegates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DelegatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Delegates model.
     * @param string $denom
     * @param string $tr_hash
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($denom, $tr_hash)
    {
        return $this->render('view', [
            'model' => $this->findModel($denom, $tr_hash),
        ]);
    }

    /**
     * Creates a new Delegates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionCharts()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $wallet = Yii::$app->request->post('wallet');

        //$waitSepExpr = new Expression("case when date + interval '30 days' <= current_timestamp then false else true end");

        $byDateExpr = new Expression("date(timezone('{$timezone}',date))");


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
            'delegate_by_day' => $deleg_ByDay,
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
        $deleg_ByDay = $deleg_ByDay
                        ->select($daySelect)->{$nextWhere}(['>','value','0'])
                        ->andWhere(['>=',$byDateExpr,$lastYearExpr])
                        ->groupBy($dayGroupBy)->orderBy($byDateExpr);


        return [
            'series'=>[
                [
                    'name' => 'Staked',
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

        $byMonthExprChr=new Expression("to_char(date_trunc('month',timezone('{$this->timezone}',date)),'YYYY-MM-DD')");
        $byMonthExpr=new Expression("date_trunc('month',timezone('{$this->timezone}',date))");


        $deleg_byMonth = Delegates::find();
        $deleg_byMonth->alias('b');
        $deleg_byMonth->leftJoin(['t' => 'tokens'],'b.denom=t.denom');
        $monthGroupBy=[$byMonthExpr];
        $monthSelect=['sum(value/pow(10,t.decimals)) as y','x'=>$byMonthExprChr];
        if($wallet) {
            $deleg_byMonth->where(['wallet'=>$wallet]);
            $monthGroupBy[]='wallet';
            $monthSelect[]='wallet';
            $nextWhere='andWhere';
        } else $nextWhere = 'where';
        $deleg_byMonth = $deleg_byMonth
                            ->select($monthSelect)->{$nextWhere}(['>','value','0'])
                            ->andWhere(['>=',$byMonthExpr,$lastYearExpr])
                            ->groupBy($monthGroupBy)->orderBy($byMonthExprChr);


        return [
            'series'=>[
                [
                    'name' => 'Staked',
                    'data' => $deleg_byMonth->asArray()->all()
                ]
            ]
        ];
    }

    /**
     * Finds the Delegates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $denom
     * @param string $tr_hash
     * @return Delegates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($denom, $tr_hash)
    {
        if (($model = Delegates::findOne(['denom' => $denom, 'tr_hash' => $tr_hash])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
