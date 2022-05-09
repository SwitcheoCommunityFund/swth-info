<?php

namespace app\controllers;

use Yii;
use app\models\TransactionsCount;
use app\models\TransactionsCountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * TransactionsCountController implements the CRUD actions for TransactionsCount model.
 */
class TransactionsCountController extends BaseController
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
     * Lists all TransactionsCount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransactionsCountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TransactionsCount model.
     * @param string $date
     * @param string $tr_type
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($date, $tr_type)
    {
        return $this->render('view', [
            'model' => $this->findModel($date, $tr_type),
        ]);
    }

    public function actionCharts($tr_type=null)
    {
        $byMonthExpr = new Expression("date_trunc('month',date)");
        $daysLimit = new Expression("current_date - interval '1 month'");
        $monthLimit = new Expression("date_trunc('month',current_date - interval '6 month')");

        $by_month = TransactionsCount::find()->select(['tr_type','sum(count) as value','date'=>$byMonthExpr])->where(['>','count','0']);
        if($tr_type!==null) $by_month->andWhere(['tr_type'=>$tr_type]);


        return $this->renderAjax('chart', [
            //'by_day' => TransactionsCount::find()->select(['tr_type','count as value','date'])->andWhere(['>','count','0'])->andWhere(['>=','date',$daysLimit])->orderBy(['date'=>'asc','tr_type'=>'asc'])->asArray()->all(),
            'by_month' => $by_month->andWhere(['>=','date',$monthLimit])->groupBy(['tr_type',$byMonthExpr])->orderBy([$byMonthExpr,'tr_type'=>'asc'])->asArray()->all(),
            'active_tr_types' => ['withdraw','send','delegate','begin_unbonding']
        ]);
    }

    public function actionPanelChart()
    {
        $currMonth = new Expression("(date_trunc('month',current_date) + interval '1 month' - interval '1 day')::date");
        $byMonthExpr = new Expression("(date_trunc('month',date) + interval '1 month' - interval '1 day')::date");
        $monthLimit = new Expression("date_trunc('month',current_date - interval '6 month')::date");

        $not_tr_types = ['create_oracle_vote'];

        $by_month = TransactionsCount::find()->select(['sum(count) as value','date'=>$byMonthExpr]);
        $by_month->where(['NOT IN','tr_type',$not_tr_types]);

        return $this->renderAjax('panel-chart', [
            'by_month' => $by_month
                ->andWhere(['>=',$byMonthExpr,$monthLimit])
                ->andWhere(['<',$byMonthExpr,$currMonth])
                ->groupBy([$byMonthExpr])->orderBy([$byMonthExpr])->asArray()->all(),
            'active_tr_types' => $not_tr_types
        ]);
    }

    public function actionChartByDay()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $lastYearExpr = new Expression("timezone('{$this->timezone}',current_date) - interval '1 year'");
        $byDateExpr = new Expression("date_trunc('day',date)");

        $not_tr_types = ['create_oracle_vote'];

        $by_date = TransactionsCount::find()->select(['x'=>$byDateExpr,'sum(count) as y']);
        $by_date->where(['NOT IN','tr_type',$not_tr_types])
                ->andWhere(['>',"timezone('{$this->timezone}',date)",$lastYearExpr])
                ->groupBy([$byDateExpr])->orderBy([$byDateExpr]);


        return [
            'series'=>[
                [
                    'name'=>'tx count',
                    'data' => $by_date->asArray()->all()
                ]
            ]
        ];
    }

    public function actionChartByMonth()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $lastYearExpr = new Expression("date_trunc('month',timezone('{$this->timezone}',current_date))- interval '12 months'");
        $byDateExpr = new Expression("left(date_trunc('month',timezone('{$this->timezone}',date))::text,10)");

        $not_tr_types = ['create_oracle_vote'];

        $by_date = TransactionsCount::find()->select(['x'=>$byDateExpr,'sum(count) as y']);
        $by_date->where(['NOT IN','tr_type',$not_tr_types])
            ->andWhere(['>',"timezone('{$this->timezone}',date)",$lastYearExpr])
            ->groupBy([$byDateExpr])->orderBy([$byDateExpr]);


        return [
            'series'=>[
                [
                    'name'=>'tx count',
                    'data' => $by_date->asArray()->all()
                ]
            ]
        ];
    }


    /**
     * Creates a new TransactionsCount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new TransactionsCount();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'date' => $model->date, 'tr_type' => $model->tr_type]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }*/

    /**
     * Updates an existing TransactionsCount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $date
     * @param string $tr_type
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionUpdate($date, $tr_type)
    {
        $model = $this->findModel($date, $tr_type);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'date' => $model->date, 'tr_type' => $model->tr_type]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }*/

    /**
     * Deletes an existing TransactionsCount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $date
     * @param string $tr_type
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    /*public function actionDelete($date, $tr_type)
    {
        $this->findModel($date, $tr_type)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the TransactionsCount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $date
     * @param string $tr_type
     * @return TransactionsCount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($date, $tr_type)
    {
        if (($model = TransactionsCount::findOne(['date' => $date, 'tr_type' => $tr_type])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
