<?php

namespace app\controllers;

use Yii;
use app\models\ExternalTransfersByWallet;
use app\models\ExternalTransfersByWalletSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExternalTransfersByWalletController implements the CRUD actions for ExternalTransfersByWallet model.
 */
class ExternalTransfersByWalletController extends Controller
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
     * Lists all ExternalTransfersByWallet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExternalTransfersByWalletSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExternalTransfersByWallet model.
     * @param string $wallet
     * @param string $blockchain
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($wallet, $blockchain)
    {
        return $this->render('view', [
            'model' => $this->findModel($wallet, $blockchain),
        ]);
    }

    /**
     * Finds the ExternalTransfersByWallet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $wallet
     * @param string $blockchain
     * @return ExternalTransfersByWallet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($wallet, $blockchain)
    {
        if (($model = ExternalTransfersByWallet::findOne(['wallet' => $wallet, 'blockchain' => $blockchain])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
