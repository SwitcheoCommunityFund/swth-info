<?php

namespace app\controllers;

use Yii;
use app\models\RewardsByWalletAndMonth;
use app\models\RewardsByWalletAndMonthSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RewardsByWalletAndMonthController implements the CRUD actions for RewardsByWalletAndMonth model.
 */
class RewardsByWalletAndMonthController extends Controller
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
     * Lists all RewardsByWalletAndMonth models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RewardsByWalletAndMonthSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RewardsByWalletAndMonth model.
     * @param string $wallet
     * @param string $month
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($wallet, $month)
    {
        return $this->render('view', [
            'model' => $this->findModel($wallet, $month),
        ]);
    }



    /**
     * Finds the RewardsByWalletAndMonth model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $wallet
     * @param string $month
     * @return RewardsByWalletAndMonth the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($wallet, $month)
    {
        if (($model = RewardsByWalletAndMonth::findOne(['wallet' => $wallet, 'month' => $month])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
