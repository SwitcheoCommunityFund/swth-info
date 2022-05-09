<?php

namespace app\controllers;

use Yii;
use app\models\ExternalTransfers;
use app\models\ExternalTransfersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExternalTransfersController implements the CRUD actions for ExternalTransfers model.
 */
class ExternalTransfersController extends Controller
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
     * Lists all ExternalTransfers models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExternalTransfersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExternalTransfers model.
     * @param string $wallet
     * @param string $transaction_hash
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($wallet, $transaction_hash)
    {
        return $this->render('view', [
            'model' => $this->findModel($wallet, $transaction_hash),
        ]);
    }

    /**
     * Finds the ExternalTransfers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $wallet
     * @param string $transaction_hash
     * @return ExternalTransfers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($wallet, $transaction_hash)
    {
        if (($model = ExternalTransfers::findOne(['wallet' => $wallet, 'transaction_hash' => $transaction_hash])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
