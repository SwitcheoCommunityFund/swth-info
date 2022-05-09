<?php

namespace app\controllers;

use Yii;
use app\models\AccountInfo;
use app\models\AccountInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;

/**
 * AccountInfoController implements the CRUD actions for AccountInfo model.
 */
class AccountInfoController extends Controller
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
     * Lists all AccountInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AccountInfo model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCharts()
    {
        $session = Yii::$app->session;
        $timezone = $session['timezone_name'];
        $timezone = empty($timezone)?'UTC':$timezone;

        $byDateExpr=new Expression("date(timezone('{$timezone}',tr_first))");
        $byMonthExpr=new Expression("date_trunc('month',timezone('{$timezone}',tr_first))");



        $by_day = AccountInfo::find()->select(['count(*) as count','date'=>$byDateExpr]);
        $by_month = AccountInfo::find()->select(['count(*) as count','date'=>$byMonthExpr]);



        return $this->renderAjax('chart', [
            'by_day' => $by_day->where(['not',['tr_first'=>null]])->groupBy([$byDateExpr])->orderBy([$byDateExpr])->asArray()->all(),
            'by_month' => $by_month->where(['not',['tr_first'=>null]])->groupBy([$byMonthExpr])->orderBy([$byMonthExpr])->asArray()->all(),
        ]);

    }


    /**
     * Finds the AccountInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AccountInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountInfo::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
